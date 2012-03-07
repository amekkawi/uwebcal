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
		
		#WebLogWindow .nslinks {
			font-size: 125%;
			padding: 6px 6px 0 14px;
		}
		
		#WebLogWindow .hideentries .entry {
			display: none;
		}
		
		#WebLogWindow .parent span, #WebLogWindow .child {
			color: #00F;
			text-decoration: underline;
		}
		
		</style><script>
		if (typeof jQuery != 'undefined' && jQuery.fn.disableTextSelection)
			(function($){
			var main, details, toggle, nsCSS = [], tree = { }, data = <?php echo CJSON::encode($logs); ?>, etime = <?php echo Yii::getLogger()->getExecutionTime(); ?>;
			
			var htmlencode = function(text) {
				return $('<div></div>').text(text).html().replace(/[\r\n]+/g, '<br/>');
			};

			var processNS = function(ns) {
				ns = ns.split('.');
				var classes = [], currTreeNode = tree;
				
				for (var n = 0; n < ns.length; n++) {
					classes.push('ns-' + ns.slice(0, n + 1).join('-'));
					nsCSS.push('#WebLogWindow .ns-' + ns.slice(0, n + 1).join('-') + ' .ns-' + ns.slice(0, n + 1).join('-') + ' {display: block !important;}');
					
					if (!(ns[n] in currTreeNode)) {
						var keys = [];
						for (var i in currTreeNode) {
							keys.push(i);
						}
						currTreeNode[ns[n]] = { '__parent': currTreeNode, '__ns': ns.slice(0, n + 1).join('.') };
					}

					currTreeNode = currTreeNode[ns[n]];
				}

				console.log(tree);
				
				return classes;
			};

			var buildNSLinks = function() {
				var nsLinks = [];
				for (var key in tree) {
					if (key.indexOf('__') != 0)
						nsLinks.push('<span class="child">' + htmlencode(key) + '</span>');
				}
				$('> .nslinks > .children', details).html(nsLinks.join(', '));
				
				if ('__parent' in tree) {
					$('> .entries', details).attr('class', 'hideentries entries ns-' + tree['__ns'].replace(/\./g, '-'));
					$('> .nslinks > .parent', details).show()
						.find('span').text(tree['__ns']);
				}
				else {
					$('> .entries', details).attr('class', 'entries');
					$('> .nslinks > .parent', details).hide();
				}
			};

			var renderDetails = function() {
				var html = [ ];
				for (var i = 0; i < data.length; i++) {
					var nsClasses = processNS(data[i][2]);

					var split = data[i][0].split(/[\n\r]+/);
					html.push('<div class="' + data[i][1] + ' ' + nsClasses.join(' ') + ' entry">'
						+ '<span class="label">' + htmlencode(split.shift()) + '</span><br/>'
						+ htmlencode(data[i][1] + ' (' + data[i][2] + ') ' + data[i][3]) + '<br/>'
						+ '<span class="tracelog">' + htmlencode(split.join("\n")) + '</span>'
						+ '</div>');
				}

				details.html('<div class="nslinks">Filter: <span class="parent" style="display: none;"><span></span> &gt;&gt; </span><span class="children"></span></div>'
						+ '<div class="etime">Execution time: ' + etime + '</div>'
						+ '<div class="entries">'
						+ html.join("\n")
						+ '</div>');
						
				buildNSLinks();
				$('head').append('<style>' + nsCSS.join("\n") + '</style>');
				console.log(nsCSS);
				renderDetails = function() {};
			};
			
			main = $('<div id="WebLogWindow" class="yui3-cssreset yui3-cssfonts yui3-cssbase"></div>')
				.appendTo('body');
			
			toggle = $('<div>Log</div>')
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
				.on('click', '.parent', function() {
					tree = tree['__parent'];
					buildNSLinks();
				})
				.on('click', '.child', function() {
					tree = tree[$(this).text()];
					buildNSLinks();
				})
				.appendTo(main);
		})(jQuery);
		
		</script><?php
		}
	}
}