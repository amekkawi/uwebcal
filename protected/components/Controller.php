<?php
/**
 * Controller class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * Controller manages a set of actions which deal with the corresponding user requests.
 *
 * Through the actions, CController coordinates the data flow between models and views.
 *
 * @property string $calendar The data for the calendar.
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.web
 */
class Controller extends CController {
	
	private $_calendar;
	private $_calendarAR;
	
	/**
	 * Get the calendar data.
	 * The calendar ID must be set to $_GET['calendarid'].
	 * @return the calendar data (see {@link CActiveRecord::getAttributes}) or NULL if it was not found.
	 * @throws CDbException
	 */
	public function getCalendar() {
		if (!isset($_GET['calendarid'])) {
			$this->_calendarAR = $this->_calendar = NULL;
		}
		elseif (!isset($this->_calendarAR)) {
			$this->_calendarAR = Calendar::model()->findByPk($_GET['calendarid']);
			$this->_calendar = $this->_calendarAR === NULL ? NULL : $this->_calendarAR->getAttributes();
		}
		return $this->_calendar;
	}
	
	/**
	 * Verify that the calendar exists, if $_GET['calendarid'] is set.
	 * 
	 * If $_GET['calendarid'] is set, and the calendar does not exist, then
	 * this method will halt execution after displaying an error or redirecting.
	 * 
	 * @return boolean true if $_GET['calendarid'] is set, false otherwise.
	 * @throws CHttpException
	 */
	protected function verifyCalendar() {
		if (isset($_GET['calendarid']) && $this->getCalendar() === NULL) {
			$exception = new CHttpException(404, Yii::t('app', 'A calendar with the ID "{calendarid}" was not found.', array('{calendarid}' => $_GET['calendarid'])));
			
			// Output JSON for AJAX errors.
			if (Yii::app()->request->isAjaxRequest) {
				Yii::app()->displayException($exception);
				Yii::app()->end();
			}
				
			// Redirect the user to a custom URL, if specified.
			elseif (Yii::app()->calendarNotFoundRedirect !== NULL) {
				$this->redirect(Yii::app()->calendarNotFoundRedirect);
			}
			
			// Show a generic 404 page.
			else {
				throw $exception;
			}
		}
		
		return isset($_GET['calendarid']);
	}
	
	/**
	 * Echo the html part specified from the calendar, if the calendar is set to use html parts. 
	 * @param string $part The name of the part, being either 'htmlheader', 'header' or 'footer'.
	 */
	protected function echoHtmlPart($part) {
		if ($this->calendar !== NULL && $this->calendar['htmlmode'] == Calendar::HTMLMODE_PARTS && $this->calendar[$part] !== NULL)
			echo $this->calendar[$part];
	}
}