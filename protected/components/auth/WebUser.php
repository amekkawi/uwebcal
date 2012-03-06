<?php
class WebUser extends CWebUser {
	
	const CACHE_KEY = '__checkcache';
	
	/**
	 * @var integer The number of minutes till the checkAccess() session cache expires.
	 */
	public $cacheMinutes = 5;
	
	/**
	 * Performs access check for this user.
	 * @param string $operation the name of the operation that need access check.
	 * @param array $params name-value pairs that would be passed to business rules associated
	 * with the tasks and roles assigned to the user.
	 * @param boolean $allowCaching whether to allow caching the result of access check.
	 * @return boolean whether the operations can be performed by this user.
	 */
	public function checkAccess($operation,$params=array(),$allowCaching=true)
	{
		$operationParts = explode(':', $operation, 2);
		if (count($operationParts) == 2) {
			$calendarId = $operationParts[0];
			$operation = $operationParts[1];
		}
		else {
			$calendarId = '*';
		}
		return $this->checkCalendarAccess($calendarId, $operation, $params, $allowCaching);
	}
	
	/**
	 * Performs access check for this user.
	 * @param string $calendarId the calendar's ID
	 * @param string $operation the name of the operation that need access check.
	 * @param array $params name-value pairs that would be passed to business rules associated
	 * with the tasks and roles assigned to the user.
	 * @param boolean $allowCaching whether to allow caching the result of access check.
	 * @return boolean whether the operations can be performed by this user.
	 */
	public function checkCalendarAccess($calendarId,$operation,$params=array(),$allowCaching=true) {
		
		// TODO: cache admin operations in _access like CWebUser::checkaccess.
		
		// Check if operation check is already cached.
		if ($allowCaching && $calendarId != '*') {
			$cache = $this->getState(self::CACHE_KEY);
			
			// Reset the cache if it expired.
			if (isset($cache['__expiration']) && $cache['__expiration'] < time())
				$this->setState(self::CACHE_KEY, $cache = NULL); //$cache = 
			
			elseif (isset($cache[$calendarId][$operation]))
				return $cache[$calendarId][$operation];
		}
		
		$check = $operation == 'view'
			? $this->hasAuthAssignment($calendarId)
			: Yii::app()->getAuthManager()->checkAccess($calendarId,$operation,$this->getId(),$params);
		
		/*if($allowCaching && $params===array() && isset($this->_access[$operation]))
			return $this->_access[$operation];
		else
			return $this->_access[$operation]=Yii::app()->getAuthManager()->checkAccess($operation,$this->getId(),$params);*/
		if ($allowCaching && $calendarId != '*' && $params === array()) {
			if (!isset($cache['__expiration']))
				$cache = array('__expiration' => time() + $this->cacheMinutes * 60);
			
			$cache[$calendarId][$operation] = $check;
			
			$this->setState(self::CACHE_KEY, $cache);
		}
		
		return $check;
	}
	
	/**
	 * Return if the user has at least one assignment to the specified calendar.
	 * 
	 * @see ICalAuthManager::hasAssignment()
	 * @param string $calendarId the calendar ID. If not set, will check admin (calendarId='*') rights.
	 * @return boolean whether the user has an assignment to the specified calendar.
	 */
	public function hasAuthAssignment($calendarId='*') {
		return Yii::app()->getAuthManager()->hasAuthAssignment($calendarId, $this->getId());
	}
	
	/**
	 * Redirects the user browser to the login page.
	 * This will set the calendarid to {@link loginUrl}, or remove it if the calendarid is not set.
	 * After calling this method, the current request processing will be terminated.
	 * @see CWebUser::loginRequired
	 */
	public function loginRequired() {
		if (isset($_GET['calendarid']))
			$this->loginUrl['calendarid'] = $_GET['calendarid'];
		else
			unset($this->loginUrl['calendarid']);
		
		parent::loginRequired();
	}
}