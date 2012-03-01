<?php
/**
 * ICalAuthManager interface is implemented by an calendar auth manager application component.
 *
 * An calendar auth manager is mainly responsible for providing role-based access control (RBAC) service.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Modified for UWebCal by André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @package system.base
 * @since 1.0
 */
interface ICalAuthManager
{
	/**
	 * Performs access check for the specified user.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the name of the operation that need access check
	 * @param mixed $userId the user ID. This should can be either an integer and a string representing
	 * the unique identifier of a user. See {@link IWebUser::getId}.
	 * @param array $params name-value pairs that would be passed to biz rules associated
	 * with the tasks and roles assigned to the user.
	 * @return boolean whether the operations can be performed by the user.
	 */
	public function checkAccess($calendarId,$itemName,$userId,$params=array());

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
	 * @return CAuthItem the authorization item
	 * @throws CException if an item with the same name already exists
	 */
	public function createAuthItem($calendarId,$name,$type,$description='',$bizRule=null,$data=null);
	/**
	 * Removes the specified authorization item.
	 * @param string $calendarId the calendar ID
	 * @param string $name the name of the item to be removed
	 * @return boolean whether the item exists in the storage and has been removed
	 */
	public function removeAuthItem($calendarId,$name);
	/**
	 * Returns the authorization items of the specific type and user.
	 * @param string $calendarId the calendar ID
	 * @param integer $type the item type (0: operation, 1: task, 2: role). Defaults to null,
	 * meaning returning all items regardless of their type.
	 * @param mixed $userId the user ID. Defaults to null, meaning returning all items even if
	 * they are not assigned to a user.
	 * @return array the authorization items of the specific type.
	 */
	public function getAuthItems($calendarId,$type=null,$userId=null);
	/**
	 * Returns the authorization item with the specified name.
	 * @param string $name the name of the item
	 * @return CAuthItem the authorization item. Null if the item cannot be found.
	 */
	public function getAuthItem($calendarId,$name);
	/**
	 * Saves an authorization item to persistent storage.
	 * @param string $calendarId the calendar ID
	 * @param CAuthItem $item the item to be saved.
	 * @param string $oldName the old item name. If null, it means the item name is not changed.
	 */
	public function saveAuthItem($calendarId,$item,$oldName=null);

	/**
	 * Adds an item as a child of another item.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the parent item name
	 * @param string $childName the child item name
	 * @throws CException if either parent or child doesn't exist or if a loop has been detected.
	 */
	public function addItemChild($calendarId,$itemName,$childName);
	/**
	 * Removes a child from its parent.
	 * Note, the child item is not deleted. Only the parent-child relationship is removed.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the parent item name
	 * @param string $childName the child item name
	 * @return boolean whether the removal is successful
	 */
	public function removeItemChild($calendarId,$itemName,$childName);
	/**
	 * Returns a value indicating whether a child exists within a parent.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the parent item name
	 * @param string $childName the child item name
	 * @return boolean whether the child exists
	 */
	public function hasItemChild($calendarId,$itemName,$childName);
	/**
	 * Returns the children of the specified item.
	 * @param string $calendarId the calendar ID
	 * @param mixed $itemName the parent item name. This can be either a string or an array.
	 * The latter represents a list of item names.
	 * @return array all child items of the parent
	 */
	public function getItemChildren($calendarId,$itemName);

	/**
	 * Assigns an authorization item to a user.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @param string $bizRule the business rule to be executed when {@link checkAccess} is called
	 * for this particular authorization item.
	 * @param mixed $data additional data associated with this assignment
	 * @return CAuthAssignment the authorization assignment information.
	 * @throws CException if the item does not exist or if the item has already been assigned to the user
	 */
	public function assign($calendarId,$itemName,$userId,$bizRule=null,$data=null);
	/**
	 * Revokes an authorization assignment from a user.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return boolean whether removal is successful
	 */
	public function revoke($calendarId,$itemName,$userId);
	/**
	 * Returns a value indicating whether the item has been assigned to the user.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return boolean whether the item has been assigned to the user.
	 */
	public function isAssigned($calendarId,$itemName,$userId);
	/**
	 * Returns the item assignment information.
	 * @param string $calendarId the calendar ID
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return CAuthAssignment the item assignment information. Null is returned if
	 * the item is not assigned to the user.
	 */
	public function getAuthAssignment($calendarId,$itemName,$userId);
	/**
	 * Returns the item assignments for the specified user.
	 * @param string $calendarId the calendar ID
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return array the item assignment information for the user. An empty array will be
	 * returned if there is no item assigned to the user.
	 */
	public function getAuthAssignments($calendarId,$userId);
	/**
	 * Saves the changes to an authorization assignment.
	 * @param string $calendarId the calendar ID
	 * @param CAuthAssignment $assignment the assignment that has been changed.
	 */
	public function saveAuthAssignment($calendarId,$assignment);

	/**
	 * Removes all authorization data.
	 * @param string $calendarId the calendar ID
	 */
	public function clearAll($calendarId);
	/**
	 * Removes all authorization assignments.
	 * @param string $calendarId the calendar ID
	 */
	public function clearAuthAssignments($calendarId);

	/**
	 * Saves authorization data into persistent storage.
	 * If any change is made to the authorization data, please make
	 * sure you call this method to save the changed data into persistent storage.
	 */
	public function save();

	/**
	 * Executes a business rule.
	 * A business rule is a piece of PHP code that will be executed when {@link checkAccess} is called.
	 * @param string $bizRule the business rule to be executed.
	 * @param array $params additional parameters to be passed to the business rule when being executed.
	 * @param mixed $data additional data that is associated with the corresponding authorization item or assignment
	 * @return whether the execution returns a true value.
	 * If the business rule is empty, it will also return true.
	 */
	public function executeBizRule($bizRule,$params,$data);
}
