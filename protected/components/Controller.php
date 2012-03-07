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
			Yii::trace('Get calendar record: ' . $_GET['calendarid'], 'application.components.Controller');
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
	 * @throws CHttpException
	 */
	protected function initCalendar() {
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
		
		// Configure the controller based on calendar settings.
		if ($this->calendar['htmlmode'] == Calendar::HTMLMODE_TEMPLATE) {
			$this->layout = 'template';
		}
	}
	
	protected function checkCalendarAccess() {
		$this->initCalendar();
		
		if (isset($_GET['calendarid'])) {
			$user = Yii::app()->user;
			$viewAuth = (int)$this->calendar['viewauth'];
			
			if ($viewAuth > Calendar::VIEWAUTH_NONE) {
				if ($user->isGuest) {
					$user->setFlash("viewauth", Yii::t('app', 'This calendar requires you to log in.'));
					$user->loginRequired();
				}
				elseif ($viewAuth == Calendar::VIEWAUTH_HASROLE
				&& !$user->checkCalendarAccess($this->calendar['calendarid'], 'view')
				&& !$user->checkAccess('view')) {
					
					$exception = new CHttpException(401, Yii::t('app','You do not have access to this calendar".'));
					
					// Output JSON for AJAX errors.
					if (Yii::app()->request->isAjaxRequest) {
						Yii::app()->displayException($exception);
						Yii::app()->end();
					}
						
					// Show a generic 401 page.
					else {
						throw $exception;
					}
				}
			}
		}
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