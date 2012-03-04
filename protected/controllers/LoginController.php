<?php
class LoginController extends Controller {
	
	public function init() {
		$this->verifyCalendar();
		
		if ($this->calendar['htmlmode'] == Calendar::HTMLMODE_TEMPLATE) {
			$this->layout = 'template';
		}
	}
	
	public function actions() {
		return CMap::mergeArray(
			array(
				'index' => 'application.controllers.login.IndexAction',
				'standard' => 'application.controllers.login.StandardAction',
			),
			Yii::app()->params['loginActions'] 
		);
	}
	
	public function actionLogout() {
		Yii::app()->user->logout();
	}
}