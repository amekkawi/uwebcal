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
	/**
	 * @var string the default layout for the controller view. Defaults to 'html'.
	 */
	public $layout = 'html';
	
	private $_calendar = NULL;
	private $_calendarAR = NULL;
	
	/**
	 * Load the calendar data. Retrieve the data using {@link Controller::getCalendar}.
	 * @param string $calendarId The calendar's ID
	 * @return boolean true if the calendar was successfully loaded, false otherwise.
	 * @throws CDbException
	 */
	protected function loadCalendar($calendarId) {
		$this->_calendarAR = Calendar::model()->findByPk($calendarId);
		
		if ($this->_calendarAR !== NULL) {
			$this->_calendar = $this->_calendarAR->getAttributes();
		}
		
		return $this->_calendarAR !== NULL;
	}
	
	/**
	 * Get the calendar data.
	 * @return the calendar data (see {@link CActiveRecord::getAttributes}).
	 */
	public function getCalendar() {
		return $this->_calendar;
	}
}