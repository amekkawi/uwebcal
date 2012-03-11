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
 * @property array eventFields An array of field objects that extend {@link FieldModel}.
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
	 * @var array TODO: 
	 */
	public $eventsActions = array();
	
	/**
	 * @var array TODO: 
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
	 * @var boolean Whether to reverse the page title order.
	 *              An example of a reversed page title would be "Login | CalendarName | App Name".
	 */
	public $reversePageTitle = true;
	
	/**
	 * @var string The prefix for CSS classes used by UWebCal.
	 */
	public $cssPrefix = 'uwc_';
	
	/**
	 * @var string The 'glue' that will be used to combine page title segments.
	 * @see implode
	 */
	public $pageTitleGlue = ' | ';
	
	private $_eventFields = array();
	
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
	 * Get an array of field objects that extend {@link FieldModel}.
	 * @return array the fields
	 */
	public function getEventFields() {
		return $this->_eventFields;
	}
	
	public function setEventFields($fields) {
		$this->_eventFields = array();
		foreach ($fields as $field) {
			$this->addEventField($field);
		}
	}
	
	public function addEventField($config) {
		if (is_array($config) && isset($config[0]) && is_string($config[0])) {
			$config['class'] = array_shift($config);
		}
		
		$field = Yii::createComponent($config);
		
		if (!($field instanceof FieldModel)) {
			throw new CHttpException(500, Yii::t('app', 'An event field must either be an array or a class that extends FieldModel.'));
		}
		
		if (!preg_match('/^[a-z][a-z0-9]*$/', $field->name))
			throw new CHttpException(500, Yii::t('app', 'The event field name "{name}" is invalid.', array('{name}' => $field->name)));
		
		if (isset($this->_eventFields[$field->name])) {
			throw new CHttpException(500, Yii::t('app', 'An event field with a name of "{name}" has already been set.', array('{name}' => $field->name)));
		}
		
		$this->_eventFields[$field->name] = $field;
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