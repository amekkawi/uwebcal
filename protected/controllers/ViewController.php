<?php

/**
 * The View controller handles non-administrative viewing of events.
 */
class ViewController extends Controller
{
	/**
	 * Default action to handle URLs that do not specify the calendar ID.
	 */
	public function actionIndex()
	{
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
		var_dump($_GET); exit;
	}
	
	/**
	 * Show events for a specific month.
	 */
	public function actionMonth() {
		var_dump($_GET); exit;
	}
	
	/**
	 * Show events for a specific week.
	 */
	public function actionWeek() {
		var_dump($_GET); exit;
	}
	
	/**
	 * Show events for a specific day.
	 */
	public function actionDay() {
		var_dump($_GET); exit;
	}
	
	/**
	 * Search for events based on criteria.
	 */
	public function actionSearch() {
		var_dump($_GET); exit;
	}
	
	/**
	 * Show a specific event's details.
	 */
	public function actionEvent() {
		var_dump($_GET); exit;
	}

	/**
	 * Handle errors.
	 */
	public function actionError()
	{
		Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/some-file.css');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/some-file.js');
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}
}