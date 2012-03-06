<?php
/**
 * WebApplication class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * WebApplication extends CWebApplication by providing functionalities specific to UWebCal.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.web
 */
class WebApplication extends CWebApplication {
	
	/**
	 * @return string the route of the default controller, action or module. Defaults to 'events'.
	 */
	public $defaultController = 'events';
	
	/**
	 * @var mixed the application-wide layout. Defaults to 'http' (relative to {@link getLayoutPath layoutPath}).
	 * If this is false, then no layout will be used.
	 */
	public $layout = 'html';
	
	/**
	 * @var string The timezone that dates will be displayed in. The default is 'UTC'.
	 *             Dates are always stored in the database in UTC.
	 */
	public $timezone = 'UTC';
	
	/**
	 * @var string The default calendar ID.
	 */
	public $defaultCalendarId = 'main';
	
	/**
	 * @var string The default 'events' view (i.e. 'upcoming', 'day')
	 */
	public $defaultViewAction = 'upcoming';
	
	/**
	 * @var string|null A URL to redirect users to if they attempt to view a calendar that
	 *                  does not exist. If set to null, a 404 page will be shown.
	 */
	public $calendarNotFoundRedirect = NULL;
	
	/**
	 * @var array 
	 */
	public $eventsActions = array();
	
	/**
	 * @var array 
	 */
	public $authActions = array();
	
	/**
	 * @var array 
	 */
	public $userIdentities = array('DbUserIdentity');
	
	/**
	 * @var string|null The name of the package to load via {@link CClientScript::registerPackage}.
	 *                  The package is registered in layout files. Set to null to skip registration.
	 */
	public $clientScriptPackage = 'app';
	
	/**
	 * Initializes the application.
	 * @see CWebApplication::init()
	 */
	protected function init() {
		parent::init();
		
		// Make sure the loginUrl is an array
		if (is_string($this->user->loginUrl))
			$this->user->loginUrl = array(Yii::app()->user->loginUrl);
	}
	
	/**
	 * Displays the captured PHP error.
	 * 
	 * If the request is an AJAX request, then this method
	 * will output JSON in a way that UWebCal expects.
	 * 
	 * @param integer $code error code
	 * @param string $message error message
	 * @param string $file error file
	 * @param string $line error line
	 * @see CApplication::displayError()
	 */
	public function displayError($code,$message,$file,$line) {
		if ($this->request->isAjaxRequest) {
			$json = array(
				'result'=>false,
				'reason'=>'exception',
				'exception'=>array(
					'type'=>'php',
					'code'=>$code,
					'message'=>$message
				)
			);
			require($this->viewPath.'/json.php');
		}
		else
			parent::displayError($code, $message, $file, $line);
	}
	
	/**
	 * Displays the uncaught PHP exception.
	 * 
	 * If the request is an AJAX request, then this method
	 * will output JSON in a way that UWebCal expects.
	 * 
	 * @param Exception $exception the uncaught exception
	 * @see CApplication::displayError()
	 */
	public function displayException($exception) {
		if ($this->request->isAjaxRequest) {
			$json = array(
				'result'=>false,
				'reason'=>'exception',
				'exception'=>array(
					'code'=>($exception instanceof CHttpException) ? $exception->statusCode : 500,
					'type'=>get_class($exception),
					'errorCode'=>$exception->getCode(),
					'message'=>$exception->getMessage()
				)
			);
			require($this->viewPath.'/json.php');
		}
		else
			parent::displayException($exception);
	}
}