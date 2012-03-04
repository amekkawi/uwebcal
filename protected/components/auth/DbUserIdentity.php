<?php
/**
 * DbUserIdentity class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; 2012 André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * DbUserIdentity represents the data needed to identify a user stored in the database.
 * 
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.auth
 */
class DbUserIdentity extends CUserIdentity {
	
	
	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$users=array(
			// username => password
			'demo'=>'demo',
			'admin'=>'admin',
		);
		if(!isset($users[$this->username]))
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if($users[$this->username]!==$this->password)
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else
			$this->errorCode=self::ERROR_NONE;
		return !$this->errorCode;
	}
}