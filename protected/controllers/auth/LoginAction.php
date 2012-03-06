<?php
class LoginAction extends CAction {
	function run() {
		if (Yii::app()->user->isGuest) {
			if (isset($_POST['username']) && isset($_POST['password'])) {
				if ($this->controller->attemptStandardLogin($_POST['username'], $_POST['password'])) {
					if (Yii::app()->request->isAjaxRequest) {
						$this->controller->renderPartial('//json', array('json'=> array(
							'result'=>true,
							'name'=>Yii::app()->user->name,
							'id'=>Yii::app()->user->id
						)));
						exit;
					}
					else {
						$returnUrl = Yii::app()->user->returnUrl;
						
						// Unset the return URL so it cannot be used again.
						Yii::app()->user->returnUrl = NULL;
						
						// Don't use the returnUrl if it points to the scriptUrl.
						// This can force the URL to include index.php even when the urlManager is being used.
						if ($returnUrl !== Yii::app()->request->scriptUrl) {
							$this->controller->redirect($returnUrl);
						}
						elseif ($this->controller->calendar !== NULL) {
							$this->controller->redirect(Yii::app()->createUrl('events', array('calendarid' => $this->controller->calendar['calendarid'])));
						}
						else {
							$this->controller->redirect(Yii::app()->homeUrl);
						}
					}
				}
				
				if (Yii::app()->request->isAjaxRequest) {
					$this->controller->renderPartial('//json', array('json'=> array(
						'result'=>false,
						'reason'=>'failedstandardauth',
						'message'=> Yii::t('app', 'The username or password you entered is incorrect.')
					)));
					exit;
				}
				
				Yii::app()->user->setFlash('failedstandardauth', Yii::t('app', 'The username or password you entered is incorrect.'));
			}
			
			$this->controller->render('standard');
		}
		elseif ($this->controller->calendar !== NULL) {
			$this->controller->redirect(Yii::app()->createUrl('events', array('calendarid' => $this->controller->calendar['calendarid'])));
		}
		else {
			$this->controller->redirect(Yii::app()->baseUrl);
		}
	}
}