<?php
/**
 * ViewController class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * The View controller handles non-administrative viewing of events.
 * @property string $calendar The data for the calendar.
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.web
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
				
				// Output JSON for AJAX errors.
				if (Yii::app()->request->isAjaxRequest) {
					Yii::app()->displayException($exception);
				}
					
				// Redirect the user to a custom URL, if specified.
				elseif (Yii::app()->params['calendarNotFoundRedirect'] !== NULL) {
					$this->redirect(Yii::app()->params['calendarNotFoundRedirect']);
				}
				
				// Show a generic 404 page.
				else {
					throw $exception;
				}
				
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
	public function actionIndex($calendarid) {
		$this->forward('/view/' . ($this->calendar['defaultview'] !== NULL ? $this->calendar['defaultview'] : Yii::app()->params['defaultViewAction']));
	}
	
	/**
	 * Show upcoming events.
	 */
	public function actionUpcoming($calendarid) {
		//Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/some-file.css');
		//Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/some-file.js');
	    $this->render('upcoming');
	}
	
	private function dateToUTime($date, $default) {
		if (!is_string($date)) $date = $default;
		
		if (($utime = strtotime($date.' 00:00:00')) === false)
			throw new CHttpException(400, Yii::t('app','Invalid date "{date}".', array('{date}'=>$date)));
		else
			return $utime; 
	}
	
	/**
	 * Show events for a specific month.
	 */
	public function actionMonth($calendarid, $date=NULL) {
		$utime = $this->dateToUTime($date.'-01', date('Y-m').'-01');
		
		// Adjust time if necessary
		if (($day = ((int)date('j', $utime))-1) >= 0)
			$utime = strtotime("-$day day", $utime);
		
		var_dump($calendarid, $date, date('r T', $utime), Yii::app()->dateFormatter->formatDateTime($utime, 'long', 'long'));
	}
	
	/**
	 * Show events for a specific week.
	 */
	public function actionWeek($calendarid, $date) {
		$utime = $this->dateToUTime($date, date('Y-m-d'));
		
		// Determine exact start day
		$weekday = (int)date('w', $utime);
		$utimeStart = strtotime("-$weekday day", $utime);
		
		var_dump($calendarid, $date, date('r T', $utime), date('r T', $utimeStart));
	}
	
	/**
	 * Show events for a specific day.
	 */
	public function actionDay($calendarid, $date=NULL) {
		$utime = $this->dateToUTime($date, date('Y-m-d'));
		
		var_dump($calendarid, $date, date('r T', $utime), Yii::app()->dateFormatter->formatDateTime($utime, 'long', 'long'));
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
}