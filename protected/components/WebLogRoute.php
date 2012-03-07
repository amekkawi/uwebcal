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
		if (Yii::app() instanceof WebApplication && !Yii::app()->getRequest()->getIsAjaxRequest()) {
		?><style>
		#WebLogWindow {
			position: absolute;
			top: 2px;
			right: 2px;
			border: 2px solid #666;
			backgroundColor: #F6F6F6;
		}
		#WebLogWindow .details {
			width: 600px;
			max-height: 500px;
			overflow: auto;
		}
		#WebLogWindow .label {
			font-size: 125%;
			color: #060;
		}
		#WebLogWindow .profile, #WebLogWindow .log, #WebLogWindow .trace {
			background-position: 4px 2px;
			background-repeat: no-repeat;
			border-left: 5px solid #090;
		}
		#WebLogWindow .profile {
			border-left-color: #C00;
		}
		#WebLogWindow .profile .label {
			color: #C00;
		}
		#WebLogWindow .trace {
			border-left-color: #00C;
		}
		#WebLogWindow .trace .label {
			color: #009;
		}
		#WebLogWindow .entry {
			margin: 6px;
			padding-left: 4px;
		}
		#WebLogWindow .tracelog {
			color: #666;
		}
		#WebLogWindow .etime {
			font-size: 125%;
			padding: 6px 6px 0 14px;
		}
		
		</style><script>
		if (typeof jQuery != 'undefined' && jQuery.fn.disableTextSelection)
			(function($){
			var main, details, toggle, renderDetails, data = <?php echo CJSON::encode($logs); ?>, etime = <?php echo Yii::getLogger()->getExecutionTime(); ?>;
			
			var htmlencode = function(text) {
				return $('<div></div>').text(text).html().replace(/[\r\n]+/g, '<br/>');
			};
			
			renderDetails = function() {
				var html = '<div class="etime">Execution time: ' + etime + '</div>';
				for (var i = 0; i < data.length; i++) {
					var split = data[i][0].split(/[\n\r]+/);
					html += '<div class="' + data[i][1] + ' entry">'
						+ '<span class="label">' + htmlencode(split.shift()) + '</span><br/>'
						+ htmlencode(data[i][1] + ' (' + data[i][2] + ') ' + data[i][3]) + '<br/>'
						+ '<span class="tracelog">' + htmlencode(split.join("\n")) + '</span>'
						+ '</div>';
				}
				//html += '</tbody></table>';
				details.html(html);
				renderDetails = function() {};
			};
			
			main = $('<div id="WebLogWindow" class="yui3-cssreset yui3-cssfonts yui3-cssbase"></div>')
				.appendTo('body');
			
			toggle = $('<div>Trace</div>')
				.disableTextSelection()
				.toggle(function() {
					details.show();
					renderDetails();
					toggle.css({
						borderBottom: '1px solid #666',
						fontSize: '125%'
					});
				}, function() {
					details.hide();
					toggle.css({
						borderBottom: '',
						fontSize: ''
					});
				})
				.css({
					backgroundColor: '#EEE',
					padding: '6px',
					cursor: 'pointer'
				})
				.appendTo(main);

			details = $('<div class="details">')
				.hide()
				.appendTo(main);
		})(jQuery);
		
		</script><?php
		}
	}
}