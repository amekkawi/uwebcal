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
		
		#WebLogWindow .categorylinks {
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
			var main, details, toggle, categoryCSS = [], tree = { }, data = <?php echo CJSON::encode($logs); ?>, etime = <?php echo Yii::getLogger()->getExecutionTime(); ?>;
			
			var htmlencode = function(text) {
				return $('<div></div>').text(text).html().replace(/[\r\n]+/g, '<br/>');
			};

			var processCategory = function(category) {
				category = category.split('.');
				var classes = [], currTreeNode = tree;
				
				for (var i = 0; i < category.length; i++) {
					// Add a category class for the entry.
					classes.push('category-' + category.slice(0, i + 1).join('-'));

					// Add a CSS rule for showing the entry when filtering by category.
					categoryCSS.push('#WebLogWindow .category-' + category.slice(0, i + 1).join('-') + ' .category-' + category.slice(0, i + 1).join('-') + ' {display: block !important;}');
					
					if (!(category[i] in currTreeNode)) {
						var keys = [];
						for (var i in currTreeNode) {
							keys.push(i);
						}
						currTreeNode[category[i]] = { '__parent': currTreeNode, '__category': category.slice(0, i + 1).join('.') };
					}

					currTreeNode = currTreeNode[category[i]];
				}

				return classes;
			};

			var buildNSLinks = function() {
				var categoryLinks = [];
				for (var key in tree) {
					if (key.indexOf('__') != 0)
						categoryLinks.push('<span class="child">' + htmlencode(key) + '</span>');
				}
				$('> .categorylinks > .children', details).html(categoryLinks.join(', '));
				
				if ('__parent' in tree) {
					$('> .entries', details).attr('class', 'hideentries entries category-' + tree['__category'].replace(/\./g, '-'));
					$('> .categorylinks > .parent', details).show()
						.find('span').text(tree['__category']);
				}
				else {
					$('> .entries', details).attr('class', 'entries');
					$('> .categorylinks > .parent', details).hide();
				}
			};
			
			var renderDetails = function() {
				var html = [ ], profileStarts = {};
				for (var i = 0; i < data.length; i++) {
					var profileExtra = '', categoryClasses = processCategory(data[i][2]);
					
					if (data[i][1] == 'profile') {
						var profileKey = data[i][0].split(/:/, 2).slice(1);
						if (profileKey in profileStarts)
							profileExtra = ' +' + (data[i][3] - profileStarts[profileKey]);
						else
							profileStarts[profileKey] = data[i][3];
					}

					var split = data[i][0].split(/[\n\r]+/);
					html.push('<div class="' + data[i][1] + ' ' + categoryClasses.join(' ') + ' entry">'
						+ '<span class="label">' + htmlencode(split.shift()) + '</span><br/>'
						+ htmlencode(data[i][1] + ' (' + data[i][2] + ') ' + data[i][3] + profileExtra) + '<br/>'
						+ '<span class="tracelog">' + htmlencode(split.join("\n")) + '</span>'
						+ '</div>');
				}

				details.html('<div class="categorylinks">Category: <span class="parent" style="display: none;"><span></span> &gt;&gt; </span><span class="children"></span></div>'
						+ '<div class="etime">Execution time: ' + etime + '</div>'
						+ '<div class="entries">'
						+ html.join("\n")
						+ '</div>');
						
				buildNSLinks();
				$('head').append('<style>' + categoryCSS.join("\n") + '</style>');
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