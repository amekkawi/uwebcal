<?php
class WebUser extends CWebUser {
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
		// TODO: cache successful checks in the session, except for admin operations (calendarId='*').
		// TODO: cache admin operations in _access like CWebUser::checkaccess.
		return Yii::app()->getAuthManager()->checkAccess($operation,$this->getId(),$params);
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
}