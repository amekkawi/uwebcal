<?php
class LoginAction extends CAction {
	function run() {
		if (isset($_POST['username']) && isset($_POST['password'])) {
			if ($this->controller->attemptStandardLogin($_POST['username'], $_POST['password'])) {
				$this->controller->redirect(Yii::app()->user->returnUrl);
			}
			Yii::app()->user->setFlash('failedstandardauth', Yii::t('app', 'The username or password you entered is incorrect.'));
		}
		
		$this->controller->render('standard');
	}
}