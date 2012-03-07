<?php
/**
 * WebLogRoute class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * WebLogRoute displays a trace log in the browser using jQuery.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.debug
 */
class WebLogRoute extends CLogRoute {
	public function processLogs($logs) {
		if (Yii::app() instanceof CWebApplication && !Yii::app()->getRequest()->getIsAjaxRequest()) {
		?><script type="text/javascript">
		if (typeof jQuery != 'undefined')
			(function($){

			// Include the CSS.
			$('head').append('<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl . '/css/weblogroute.css'; ?>" />');

			// Include the JavaScript
			$('head').append('<'+'script type="text/javascript" src="<?php echo Yii::app()->baseUrl . '/js/weblogroute.js'; ?>"><'+'/script>');
			
			WEBLOGROUTE($, <?php echo CJSON::encode($logs); ?>, etime = <?php echo Yii::getLogger()->getExecutionTime(); ?>);
		})(jQuery);
		
		</script><?php
		}
	}
}