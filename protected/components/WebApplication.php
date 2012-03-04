<?php
class WebApplication extends CWebApplication {
	public function displayError($code,$message,$file,$line) {
		if ($this->request->isAjaxRequest) {
			$json = array(
				'result'=>false,
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
	
	public function displayException($exception) {
		if ($this->request->isAjaxRequest) {
			$json = array(
				'result'=>false,
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