<?php
/**
 * DbAuthManager class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.yiiframework.com/
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * DbAuthManager represents an authorization manager that stores authorization information in database.
 *
 * The database connection is specified by {@link connectionID}. You may change
 * the names of the three tables used to store the authorization data by setting
 * {@link itemTable}, {@link itemChildTable} and {@link assignmentTable}.
 *
 * @property array $authItems The authorization items of the specific type.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.auth.rbac
 */
class DbAuthManager extends AuthManager
{
	/**
	 * @var string the ID of the {@link CDbConnection} application component. Defaults to 'db'.
	 */
	public $connectionID='db';
	/**
	 * @var string the name of the table storing authorization items. Defaults to 'CalAuthItem'.
	 */
	public $itemTable='CalAuthItem';
	/**
	 * @var string the name of the table storing authorization item hierarchy. Defaults to 'AuthItemChild'.
	 */
	public $itemChildTable='AuthItemChild';
	/**
	 * @var string the name of the table storing authorization item assignments. Defaults to 'CalAuthAssignment'.
	 */
	public $assignmentTable='CalAuthAssignment';
	/**
	 * @var CDbConnection the database connection. By default, this is initialized
	 * automatically as the application component whose ID is indicated as {@link connectionID}.
	 */
	public $db;
	/**
	 * @var boolean whether the the tables use foreign key relationships to cascade updates and deletes.
	 * Defaults to true, meaning that the tables use foreign key relationships. Always false for 'sqlite' databases.
	 */
	public $useForeignKeys=true;

	private $_usingSqlite;

	/**
	 * Initializes the application component.
	 * This method overrides the parent implementation by establishing the database connection.
	 */
	public function init()
	{
		parent::init();
		$this->_usingSqlite= !$this->useForeignKeys || !strncmp($this->getDbConnection()->getDriverName(),'sqlite',6) ;
	}

	/**
	 * Performs access check for the specified user.
	 * @param string $calendarId optional calendar ID
	 * @param string $itemName the name of the operation that need access check
	 * @param mixed $userId the user ID. This should can be either an integer and a string representing
	 * the unique identifier of a user. See {@link IWebUser::getId}.
	 * @param array $params name-value pairs that would be passed to biz rules associated
	 * with the tasks and roles assigned to the user.
	 * @return boolean whether the operations can be performed by the user.
	 */
	public function checkAccess($calendarId,$itemName,$userId=array(),$params=array())
	{
		if (is_array($userId)) {
			$params = $userId;
			$userId = $itemName;
			$itemName = $calendarId;
			$calendarId = NULL;
			
			$itemNameParts = explode(':', $itemName, 2);
			if (count($itemNameParts) == 2) {
				$calendarId = $itemNameParts[0];
				$itemName = $itemNameParts[1];
			}
		}
		
		$assignments=$this->getAuthAssignments($calendarId,$userId);
		return $this->checkAccessRecursive($calendarId,$itemName,$userId,$params,$assignments);
	}

	/**
	 * Performs access check for the specified user.
	 * This method is internally called by {@link checkAccess}.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the name of the operation that need access check
	 * @param mixed $userId the user ID. This should can be either an integer and a string representing
	 * the unique identifier of a user. See {@link IWebUser::getId}.
	 * @param array $params name-value pairs that would be passed to biz rules associated
	 * with the tasks and roles assigned to the user.
	 * @param array $assignments the assignments to the specified user
	 * @return boolean whether the operations can be performed by the user.
	 * @since 1.1.3
	 */
	protected function checkAccessRecursive($calendarId,$itemName,$userId,$params,$assignments)
	{
		if(($item=$this->getAuthItem($calendarId,$itemName))===null)
			return false;
		Yii::trace('Checking permission "'.$item->getName().'"','application.components.auth.rbac.DbAuthManager');
		if($this->executeBizRule($item->getBizRule(),$params,$item->getData()))
		{
			if(in_array($itemName,$this->defaultRoles))
				return true;
			if(isset($assignments[$itemName]))
			{
				$assignment=$assignments[$itemName];
				if($this->executeBizRule($assignment->getBizRule(),$params,$assignment->getData()))
					return true;
			}
			$parents=$this->db->createCommand()
				->select('parent')
				->from($this->itemChildTable)
				->where('calendarid=:calendarid AND child=:name', array(':calendarid'=>$calendarId,':name'=>$itemName))
				->queryColumn();
			foreach($parents as $parent)
			{
				if($this->checkAccessRecursive($calendarId,$parent,$userId,$params,$assignments))
					return true;
			}
		}
		return false;
	}

	/**
	 * Adds an item as a child of another item.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the parent item name
	 * @param string $childName the child item name
	 * @return boolean whether the item is added successfully
	 * @throws CException if either parent or child doesn't exist or if a loop has been detected.
	 */
	public function addItemChild($calendarId,$itemName,$childName)
	{
		if($itemName===$childName)
			throw new CException(Yii::t('yii','Cannot add "{name}" as a child of itself.',
					array('{name}'=>$itemName)));

		$rows=$this->db->createCommand()
			->select()
			->from($this->itemTable)
			->where('calendarid=:calendarid AND (name=:name1 OR name=:name2)', array(
				':calendarid'=>$calendarId,
				':name1'=>$itemName,
				':name2'=>$childName
			))
			->queryAll();

		if(count($rows)==2)
		{
			if($rows[0]['name']===$itemName)
			{
				$parentType=$rows[0]['type'];
				$childType=$rows[1]['type'];
			}
			else
			{
				$childType=$rows[0]['type'];
				$parentType=$rows[1]['type'];
			}
			$this->checkItemChildType($parentType,$childType);
			if($this->detectLoop($calendarId, $itemName,$childName))
				throw new CException(Yii::t('yii','Cannot add "{child}" as a child of "{name}". A loop has been detected.',
					array('{child}'=>$childName,'{name}'=>$itemName)));

			$this->db->createCommand()
				->insert($this->itemChildTable, array(
					'calendarid'=>$calendarId,
					'parent'=>$itemName,
					'child'=>$childName,
				));

			return true;
		}
		else
			throw new CException(Yii::t('yii','Either "{parent}" or "{child}" does not exist.',array('{child}'=>$childName,'{parent}'=>$itemName)));
	}

	/**
	 * Removes a child from its parent.
	 * Note, the child item is not deleted. Only the parent-child relationship is removed.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the parent item name
	 * @param string $childName the child item name
	 * @return boolean whether the removal is successful
	 */
	public function removeItemChild($calendarId,$itemName,$childName)
	{
		return $this->db->createCommand()
			->delete($this->itemChildTable, 'calendarid=:calendarid AND parent=:parent AND child=:child', array(
				':calendarid'=>$calendarId,
				':parent'=>$itemName,
				':child'=>$childName
			)) > 0;
	}

	/**
	 * Returns a value indicating whether a child exists within a parent.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the parent item name
	 * @param string $childName the child item name
	 * @return boolean whether the child exists
	 */
	public function hasItemChild($calendarId,$itemName,$childName)
	{
		return $this->db->createCommand()
			->select('parent')
			->from($this->itemChildTable)
			->where('calendarid=:calendarid AND parent=:parent AND child=:child', array(
				':calendarid'=>$calendarId,
				':parent'=>$itemName,
				':child'=>$childName))
			->queryScalar() !== false;
	}

	/**
	 * Returns the children of the specified item.
	 * @param string $calendarId the calendar ID
	 * @param mixed $names the parent item name. This can be either a string or an array.
	 * The latter represents a list of item names.
	 * @return array all child items of the parent
	 */
	public function getItemChildren($calendarId,$names)
	{
		if(is_string($names))
			$condition='parent='.$this->db->quoteValue($names);
		else if(is_array($names) && $names!==array())
		{
			foreach($names as &$name)
				$name=$this->db->quoteValue($name);
			$condition='parent IN ('.implode(', ',$names).')';
		}

		$rows=$this->db->createCommand()
			->select('t1.calendarid, name, type, description, bizrule, data')
			->from(array(
				$this->itemTable.' t1',
				$this->itemChildTable.' t2'
			))
			->where('t1.calendarid=:calendarid AND t1.calendarid=t2.calendarid AND ' . $condition.' AND name=child', array(':calendarid'=>$calendarId))
			->queryAll();

		$children=array();
		foreach($rows as $row)
		{
			if(($data=@unserialize($row['data']))===false)
				$data=null;
			$children[$row['name']]=new CalAuthItem($this,$row['calendarid'],$row['name'],$row['type'],$row['description'],$row['bizrule'],$data);
		}
		return $children;
	}

	/**
	 * Assigns an authorization item to a user.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @param string $bizRule the business rule to be executed when {@link checkAccess} is called
	 * for this particular authorization item.
	 * @param mixed $data additional data associated with this assignment
	 * @return CalAuthAssignment the authorization assignment information.
	 * @throws CException if the item does not exist or if the item has already been assigned to the user
	 */
	public function assign($calendarId,$itemName,$userId,$bizRule=null,$data=null)
	{
		if($this->usingSqlite() && $this->getAuthItem($calendarId,$itemName)===null)
			throw new CException(Yii::t('yii','The item "{name}" does not exist.',array('{name}'=>$itemName)));

		$this->db->createCommand()
			->insert($this->assignmentTable, array(
				'calendarid'=>$calendarId,
				'itemname'=>$itemName,
				'userid'=>$userId,
				'bizrule'=>$bizRule,
				'data'=>serialize($data)
			));
		return new CalAuthAssignment($this,$calendarId,$itemName,$userId,$bizRule,$data);
	}

	/**
	 * Revokes an authorization assignment from a user.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return boolean whether removal is successful
	 */
	public function revoke($calendarId,$itemName,$userId)
	{
		return $this->db->createCommand()
			->delete($this->assignmentTable, 'calendarid=:calendarid AND itemname=:itemname AND userid=:userid', array(
				':calendarid'=>$calendarId,
				':itemname'=>$itemName,
				':userid'=>$userId
			)) > 0;
	}

	/**
	 * Returns a value indicating whether the item has been assigned to the user.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return boolean whether the item has been assigned to the user.
	 */
	public function isAssigned($calendarId,$itemName,$userId)
	{
		return $this->db->createCommand()
			->select('itemname')
			->from($this->assignmentTable)
			->where('calendarid=:calendarid AND itemname=:itemname AND userid=:userid', array(
				':calendarid'=>$calendarId,
				':itemname'=>$itemName,
				':userid'=>$userId))
			->queryScalar() !== false;
	}

	/**
	 * Returns the item assignment information.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return CalAuthAssignment the item assignment information. Null is returned if
	 * the item is not assigned to the user.
	 */
	public function getAuthAssignment($calendarId,$itemName,$userId)
	{
		$row=$this->db->createCommand()
			->select()
			->from($this->assignmentTable)
			->where('calendarid=:calendarid AND itemname=:itemname AND userid=:userid', array(
				':calendarid'=>$calendarId,
				':itemname'=>$itemName,
				':userid'=>$userId))
			->queryRow();
		if($row!==false)
		{
			if(($data=@unserialize($row['data']))===false)
				$data=null;
			return new CalAuthAssignment($this,$row['calendarid'],$row['itemname'],$row['userid'],$row['bizrule'],$data);
		}
		else
			return null;
	}

	/**
	 * Returns the item assignments for the specified user.
	 * @param string $calendarId the calendar ID
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return array the item assignment information for the user. An empty array will be
	 * returned if there is no item assigned to the user.
	 */
	public function getAuthAssignments($calendarId,$userId)
	{
		$rows=$this->db->createCommand()
			->select()
			->from($this->assignmentTable)
			->where('calendarid=:calendarid AND userid=:userid', array(':calendarid'=>$calendarId,':userid'=>$userId))
			->queryAll();
		$assignments=array();
		foreach($rows as $row)
		{
			if(($data=@unserialize($row['data']))===false)
				$data=null;
			$assignments[$row['itemname']]=new CalAuthAssignment($this,$row['calendarid'],$row['itemname'],$row['userid'],$row['bizrule'],$data);
		}
		return $assignments;
	}

	/**
	 * Saves the changes to an authorization assignment.
	 * @param CalAuthAssignment $assignment the assignment that has been changed.
	 */
	public function saveAuthAssignment(CalAuthAssignment $assignment)
	{
		$this->db->createCommand()
			->update($this->assignmentTable, array(
				'bizrule'=>$assignment->getBizRule(),
				'data'=>serialize($assignment->getData()),
			), 'calendarid=:calendarid AND itemname=:itemname AND userid=:userid', array(
				'calendarid'=>$assignment->getCalendarId(),
				'itemname'=>$assignment->getItemName(),
				'userid'=>$assignment->getUserId()
			));
	}
	
	/**
	 * Return if the specified user has at least one assignment to the specified calendar.
	 * 
	 * @param string $calendarId the calendar ID
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return boolean whether the user has an assignment to the specified calendar.
	 */
	public function hasAuthAssignment($calendarId, $userId) {
		return ((int)$this->db->createCommand()
			->select('count(*) as num')
			->from($this->assignmentTable)
			->where('calendarid=:calendarid AND userid=:userid', array(':calendarid'=>$calendarId,':userid'=>$userId))
			->queryScalar()) > 0;
	}

	/**
	 * Returns the authorization items of the specific type and user.
	 * @param string $calendarId the calendar ID
	 * @param integer $type the item type (0: operation, 1: task, 2: role). Defaults to null,
	 * meaning returning all items regardless of their type.
	 * @param mixed $userId the user ID. Defaults to null, meaning returning all items even if
	 * they are not assigned to a user.
	 * @return array the authorization items of the specific type.
	 */
	public function getAuthItems($calendarId,$type=null,$userId=null)
	{
		if($type===null && $userId===null)
		{
			$command=$this->db->createCommand()
				->select()
				->from($this->itemTable)
				->where('calendarid=:calendarid', array(':calendarid'=>$calendarId));
		}
		else if($userId===null)
		{
			$command=$this->db->createCommand()
				->select()
				->from($this->itemTable)
				->where('calendarid=:calendarid AND type=:type', array(':calendarid'=>$calendarId, ':type'=>$type));
		}
		else if($type===null)
		{
			$command=$this->db->createCommand()
				->select('name,type,description,t1.bizrule,t1.data')
				->from(array(
					$this->itemTable.' t1',
					$this->assignmentTable.' t2'
				))
				->where('t1.calendarid=:calendarid AND t1.calendarid=t2.calendarid AND name=itemname AND userid=:userid', array(':calendarid'=>$calendarId, ':userid'=>$userId));
		}
		else
		{
			$command=$this->db->createCommand()
				->select('name,type,description,t1.bizrule,t1.data')
				->from(array(
					$this->itemTable.' t1',
					$this->assignmentTable.' t2'
				))
				->where('t1.calendarid=:calendarid AND t1.calendarid=t2.calendarid AND name=itemname AND type=:type AND userid=:userid', array(
					':calendarid'=>$calendarId,
					':type'=>$type,
					':userid'=>$userId
				));
		}
		$items=array();
		foreach($command->queryAll() as $row)
		{
			if(($data=@unserialize($row['data']))===false)
				$data=null;
			$items[$row['name']]=new CalAuthItem($this,$row['calendarid'],$row['name'],$row['type'],$row['description'],$row['bizrule'],$data);
		}
		return $items;
	}

	/**
	 * Creates an authorization item.
	 * An authorization item represents an action permission (e.g. creating a post).
	 * It has three types: operation, task and role.
	 * Authorization items form a hierarchy. Higher level items inheirt permissions representing
	 * by lower level items.
	 * @param string $calendarId the calendar ID
	 * @param string $name the item name. This must be a unique identifier.
	 * @param integer $type the item type (0: operation, 1: task, 2: role).
	 * @param string $description description of the item
	 * @param string $bizRule business rule associated with the item. This is a piece of
	 * PHP code that will be executed when {@link checkAccess} is called for the item.
	 * @param mixed $data additional data associated with the item.
	 * @return CalAuthItem the authorization item
	 * @throws CException if an item with the same name already exists
	 */
	public function createAuthItem($calendarId,$name,$type,$description='',$bizRule=null,$data=null)
	{
		$this->db->createCommand()
			->insert($this->itemTable, array(
				'calendarid'=>$calendarId,
				'name'=>$name,
				'type'=>$type,
				'description'=>$description,
				'bizrule'=>$bizRule,
				'data'=>serialize($data)
			));
		return new CalAuthItem($this,$calendarId,$name,$type,$description,$bizRule,$data);
	}

	/**
	 * Removes the specified authorization item.
	 * @param string $calendarId the calendar ID
	 * @param string $name the name of the item to be removed
	 * @return boolean whether the item exists in the storage and has been removed
	 */
	public function removeAuthItem($calendarId,$name)
	{
		if($this->usingSqlite())
		{
			$this->db->createCommand()
				->delete($this->itemChildTable, 'calendarid=:calendarid AND (parent=:name1 OR child=:name2)', array(
					':calendarid'=>$calendarId,
					':name1'=>$name,
					':name2'=>$name
			));
			$this->db->createCommand()
				->delete($this->assignmentTable, 'calendarid=:calendarid AND itemname=:name', array(
					':calendarid'=>$calendarId,
					':name'=>$name,
			));
		}

		return $this->db->createCommand()
			->delete($this->itemTable, 'calendarid=:calendarid AND name=:name', array(
				':calendarid'=>$calendarId,
				':name'=>$name
			)) > 0;
	}

	/**
	 * Returns the authorization item with the specified name.
	 * @param string $calendarId the calendar ID
	 * @param string $name the name of the item
	 * @return CalAuthItem the authorization item. Null if the item cannot be found.
	 */
	public function getAuthItem($calendarId,$name)
	{
		$row=$this->db->createCommand()
			->select()
			->from($this->itemTable)
			->where('calendarid=:calendarid AND name=:name', array(':calendarid'=>$calendarId, ':name'=>$name))
			->queryRow();

		if($row!==false)
		{
			if(($data=@unserialize($row['data']))===false)
				$data=null;
			return new CalAuthItem($this,$row['calendarid'],$row['name'],$row['type'],$row['description'],$row['bizrule'],$data);
		}
		else
			return null;
	}

	/**
	 * Saves an authorization item to persistent storage.
	 * @param CalAuthItem $item the item to be saved.
	 * @param string $oldName the old item name. If null, it means the item name is not changed.
	 */
	public function saveAuthItem(CalAuthItem $item,$oldName=null)
	{
		if($this->usingSqlite() && $oldName!==null && $item->getName()!==$oldName)
		{
			$this->db->createCommand()
				->update($this->itemChildTable, array(
					'parent'=>$item->getName(),
				), 'calendarid=:calendarid AND parent=:whereName', array(
					':calendarid'=>$item->getCalendarId(),
					':whereName'=>$oldName,
				));
			$this->db->createCommand()
				->update($this->itemChildTable, array(
					'child'=>$item->getName(),
				), 'calendarid=:calendarid AND child=:whereName', array(
					':calendarid'=>$item->getCalendarId(),
					':whereName'=>$oldName,
				));
			$this->db->createCommand()
				->update($this->assignmentTable, array(
					'itemname'=>$item->getName(),
				), 'calendarid=:calendarid AND itemname=:whereName', array(
					':calendarid'=>$item->getCalendarId(),
					':whereName'=>$oldName,
				));
		}

		$this->db->createCommand()
			->update($this->itemTable, array(
				'name'=>$item->getName(),
				'type'=>$item->getType(),
				'description'=>$item->getDescription(),
				'bizrule'=>$item->getBizRule(),
				'data'=>serialize($item->getData()),
			), 'calendarid=:calendarid AND name=:whereName', array(
				':calendarid'=>$item->getCalendarId(),
				':whereName'=>$oldName===null?$item->getName():$oldName,
			));
	}

	/**
	 * Saves the authorization data to persistent storage.
	 */
	public function save()
	{
	}

	/**
	 * Removes all authorization data.
	 */
	public function clearAll($calendarId=NULL)
	{
		$conditions='';
		$params=array();
		if (!is_null($calendarId)) {
			$conditions='calendarid=:calendarid';
			$params[':calendarid'] = $calendarId; 
		}
		
		$this->clearAuthAssignments($calendarId);
		$this->db->createCommand()->delete($this->itemChildTable, $conditions, $params);
		$this->db->createCommand()->delete($this->itemTable, $conditions, $params);
	}

	/**
	 * Removes all authorization assignments.
	 */
	public function clearAuthAssignments($calendarId=NULL)
	{
		$conditions='';
		$params=array();
		if (!is_null($calendarId)) {
			$conditions='calendarid=:calendarid';
			$params[':calendarid'] = $calendarId; 
		}
		
		$this->db->createCommand()->delete($this->assignmentTable, $conditions, $params);
	}

	/**
	 * Checks whether there is a loop in the authorization item hierarchy.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName parent item name
	 * @param string $childName the name of the child item that is to be added to the hierarchy
	 * @return boolean whether a loop exists
	 */
	protected function detectLoop($calendarId,$itemName,$childName)
	{
		if($childName===$itemName)
			return true;
		foreach($this->getItemChildren($calendarId,$childName) as $child)
		{
			if($this->detectLoop($calendarId,$itemName,$child->getName()))
				return true;
		}
		return false;
	}

	/**
	 * @return CDbConnection the DB connection instance
	 * @throws CException if {@link connectionID} does not point to a valid application component.
	 */
	protected function getDbConnection()
	{
		if($this->db!==null)
			return $this->db;
		else if(($this->db=Yii::app()->getComponent($this->connectionID)) instanceof CDbConnection)
			return $this->db;
		else
			throw new CException(Yii::t('yii','CDbAuthManager.connectionID "{id}" is invalid. Please make sure it refers to the ID of a CDbConnection application component.',
				array('{id}'=>$this->connectionID)));
	}

	/**
	 * @return boolean whether the database is a SQLite database
	 */
	protected function usingSqlite()
	{
		return $this->_usingSqlite;
	}
}
