<?php
class StandardAction extends CAction {
	function run() {
		if (isset($_POST['username']) && isset($_POST['password'])) {
			foreach (Yii::app()->params['userIdentities'] as $identityClass) {
				$identity = new $identityClass($_POST['username'], $_POST['password']);
				if ($identity->authenticate()) {
					Yii::app()->user->login($identity);
					$this->controller->redirect(Yii::app()->user->returnUrl);
				}
			}
			Yii::app()->user->setFlash('failedstandardauth', Yii::t('app', 'The username or password you entered is incorrect.'));
		}
		
		$this->controller->render('standard');
	}
}