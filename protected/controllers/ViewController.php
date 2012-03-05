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
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.web
 */
class ViewController extends Controller {
	
	public function init() {
		if (!isset($_GET['calendarid'])) {
			$_GET['calendarid'] = Yii::app()->params['defaultCalendarId'];
		}
		
		$this->verifyCalendar();
		
		$user = Yii::app()->user;
		$viewAuth = (int)$this->calendar['viewauth'];
		
		if ($viewAuth > Calendar::VIEWAUTH_NONE) {
			if ($user->isGuest) {
				$user->setFlash("viewauth", Yii::t('app', 'The "{calendar}" calendar requires you to log in before viewing events.', array('{calendar}' => $this->calendar['name'])));
				$user->loginUrl['calendarid'] = $_GET['calendarid'];
				$user->loginRequired();
			}
			elseif ($viewAuth == Calendar::VIEWAUTH_HASROLE
			&& !$user->checkCalendarAccess($this->calendar['calendarid'], 'view')
			&& !$user->checkAccess('view')) {
				
				$exception = new CHttpException(401, Yii::t('app','You do not have access to the "{calendarid}" calendar".', array('{calendarid}'=>$this->calendar['calendarid'])));
				
				// Output JSON for AJAX errors.
				if (Yii::app()->request->isAjaxRequest) {
					Yii::app()->displayException($exception);
					exit;
				}
					
				// Show a generic 401 page.
				else {
					throw $exception;
				}
			}
		}
		
		if ($this->calendar['htmlmode'] == Calendar::HTMLMODE_TEMPLATE) {
			$this->layout = 'template';
		}
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
	public function actionWeek($calendarid, $date=NULL) {
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