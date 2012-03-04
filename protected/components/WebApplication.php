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