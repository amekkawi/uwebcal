<?php
/**
 * CalAuthAssignment class file.
 * 
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CalAuthAssignment represents an assignment of a role to a user.
 * It includes additional assignment information such as {@link bizRule} and {@link data}.
 * Do not create a CalAuthAssignment instance using the 'new' operator.
 * Instead, call {@link ICalAuthManager::assign}.
 *
 * @property mixed $userId User ID (see {@link IWebUser::getId}).
 * @property string $itemName The authorization item name.
 * @property string $bizRule The business rule associated with this assignment.
 * @property mixed $data Additional data for this assignment.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author André Mekkawi <uwebcal@andremekkawi.com> 
 * @package app.auth.rbac
 */
class CalAuthAssignment extends CComponent
{
	private $_auth;
	private $_calendarId;
	private $_itemName;
	private $_userId;
	private $_bizRule;
	private $_data;

	/**
	 * Constructor.
	 * @param ICalAuthManager $auth the authorization manager
	 * @param string $calendarId the calendar ID
	 * @param string $itemName authorization item name
	 * @param mixed $userId user ID (see {@link IWebUser::getId})
	 * @param string $bizRule the business rule associated with this assignment
	 * @param mixed $data additional data for this assignment
	 */
	public function __construct(ICalAuthManager $auth,$calendarId,$itemName,$userId,$bizRule=null,$data=null)
	{
		$this->_auth=$auth;
		$this->_calendarId=$calendarId;
		$this->_itemName=$itemName;
		$this->_userId=$userId;
		$this->_bizRule=$bizRule;
		$this->_data=$data;
	}

	/**
	 * @return string the calendar ID
	 */
	public function getCalendarId()
	{
		return $this->_calendarId;
	}

	/**
	 * @return mixed user ID (see {@link IWebUser::getId})
	 */
	public function getUserId()
	{
		return $this->_userId;
	}

	/**
	 * @return string the authorization item name
	 */
	public function getItemName()
	{
		return $this->_itemName;
	}

	/**
	 * @return string the business rule associated with this assignment
	 */
	public function getBizRule()
	{
		return $this->_bizRule;
	}

	/**
	 * @param string $value the business rule associated with this assignment
	 */
	public function setBizRule($value)
	{
		if($this->_bizRule!==$value)
		{
			$this->_bizRule=$value;
			$this->_auth->saveAuthAssignment($this);
		}
	}

	/**
	 * @return mixed additional data for this assignment
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * @param mixed $value additional data for this assignment
	 */
	public function setData($value)
	{
		if($this->_data!==$value)
		{
			$this->_data=$value;
			$this->_auth->saveAuthAssignment($this);
		}
	}
}