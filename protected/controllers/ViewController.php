<?php

/**
 * The View controller handles non-administrative viewing of events.
 * @property string $calendar The data for the calendar.
 */
class ViewController extends Controller {
	
	private $_calendar = NULL;
	private $_calendarAR = NULL;
	
	public function init() {
		if (!isset($_GET['calendarid'])) {
			$_GET['calendarid'] = Yii::app()->params['defaultCalendarId'];
		}
		if ($this->action != "error") {
			$this->_calendarAR = Calendar::model()->findByPk($_GET['calendarid']);
			if ($this->_calendarAR !== NULL) {
				$this->_calendar = $this->_calendarAR->getAttributes();
			}
			else {
				$exception = new CHttpException(404, Yii::t('app', 'A calendar with the ID "{calendarid}" was not found.', array('{calendarid}' => $_GET['calendarid'])));
				if (Yii::app()->request->isAjaxRequest)
					Yii::app()->displayException($exception);
				elseif (Yii::app()->params['calendarNotFoundRedirect'] !== NULL)
					$this->redirect(Yii::app()->params['calendarNotFoundRedirect']);
				else
					throw new CHttpException(404, Yii::t('app', 'A calendar with the ID "{calendarid}" was not found.', array('{calendarid}' => $_GET['calendarid'])));
				exit;
			}
		}
	}
	
	/**
	 * Get the calendar data.
	 * @return the calendar data (see {@link CActiveRecord::getAttributes}).
	 */
	public function getCalendar() {
		return $this->_calendar;
	}
	
	/**
	 * Default action to handle URLs that do not specify the calendar ID.
	 */
	public function actionIndex() {
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		if (!isset($_GET['calendarid'])) {
			$_GET['calendarid'] = Yii::app()->params['defaultCalendarId'];
		}
		
		// TODO: Determine the default view action for the specific calendar from the DB.
		$this->forward('/view/' . Yii::app()->params['defaultViewAction']);
	}
	
	/**
	 * Show upcoming events.
	 */
	public function actionUpcoming() {
		$this->render('upcoming');
	}
	
	/**
	 * Show events for a specific month.
	 */
	public function actionMonth($calendarid,$date) {
		var_dump($_GET);
	}
	
	/**
	 * Show events for a specific week.
	 */
	public function actionWeek() {
		var_dump($_GET);
	}
	
	/**
	 * Show events for a specific day.
	 */
	public function actionDay() {
		var_dump($_GET);
	}
	
	/**
	 * Search for events based on criteria.
	 */
	public function actionSearch() {
		var_dump($_GET);
	}
	
	/**
	 * Show a specific event's details.
	 */
	public function actionEvent() {
		var_dump($_GET);
	}

	/**
	 * Handle errors.
	 */
	public function actionError() {
		Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/some-file.css');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/some-file.js');
	    if($error = Yii::app()->errorHandler->error) {
	    	if(Yii::app()->request->isAjaxRequest) {
	    		$this->render('/ajax', array('json' => array(
	    			'result'=>false,
	    			'internalerror'=>array(
	    				'type'=>$error['type'],
	    				'message'=>$error['message'],
	    				'errorCode'=>isset($error['errorCode']) ? $error['errorCode'] : null,
	    				'code'=>$error['code']
	    			)
	    		)));
	    	}
	    	elseif ($error['type'] == "CHttpException") {
	    		$this->render('/errors/http', $error);
	    	}
	    	elseif ($error['type'] == "CDbException") {
	    		$this->render('/errors/db', $error);
	    	}
	    	else {
	        	$this->render('/errors/runtime', $error);
	    	}
	    }
	}
}