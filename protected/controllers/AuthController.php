<?php
class AuthController extends Controller {
	
	public function init() {
		$this->verifyCalendar();
		
		if ($this->calendar['htmlmode'] == Calendar::HTMLMODE_TEMPLATE) {
			$this->layout = 'template';
		}
	}
	
	public function actions() {
		return CMap::mergeArray(
			array(
				'login' => 'application.controllers.auth.LoginAction'
			),
			Yii::app()->params['authActions'] 
		);
	}
	
	public function actionIndex() {
		$this->forward('auth/login');
	}
	
	public function actionLogout() {
		Yii::app()->user->logout();
		
		if ($this->calendar !== NULL) {
			$this->redirect(Yii::app()->createUrl('view', array('calendarid' => $this->calendar['calendarid'])));
		}
		else {
			$this->redirect(Yii::app()->createUrl('auth/login'));
		}
	}
	
	/**
	 * Try to authenticate and login (see {@link CWebUser::login}) the specified username
	 * and password using the list of CUserIdentity classes defined by Yii::app()->params.
	 * 
	 * @param string $username
	 * @param string $password
	 * @return boolean true if we successfully authenticated and logged in the user, otherwise false.
	 */
	public function attemptStandardLogin($username, $password) {
		foreach (Yii::app()->params['userIdentities'] as $identityClass) {
			$identity = new $identityClass($username, $password);
			if ($identity->authenticate()) {
				return Yii::app()->user->login($identity);
			}
		}
		return false;
	}
}