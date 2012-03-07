window.WEBLOGROUTE = function($, data) {
	var main, details, toggle, categoryList = [], categoryCSS = [];
	
	var htmlencode = function(text) {
		return $('<div></div>').text(text + '').html().replace(/[\r\n]+/g, '<br/>');
	};

	var resize = function() {
		if (details.is(':visible'))
			details.css('max-height', ($(window).innerHeight() - details.position().top - (main.outerHeight(true) - main.innerHeight()) - 4) + 'px');
	};

	var categoryComparator = function(a, b) {
		var splitA = ($.isDefined(a.name) ? a.name : a).toLowerCase().split(/\./g),
			splitB = ($.isDefined(b.name) ? b.name : b).toLowerCase().split(/\./g);

		for (var i = 0; i < splitA.length; i++) {
			if (i >= splitB.length) return 1;
			else if (splitA[i] < splitB[i]) return -1;
			else if (splitA[i] > splitB[i]) return 1;
		}

		if (splitB.length > splitA.length) return -1;

		return 0;
	};

	var processCategory = function(category) {
		category = category.split('.');
		var classes = [];
		
		for (var i = 0; i < category.length; i++) {
			// Add a category class for the entry.
			classes.push('category-' + category.slice(0, i + 1).join('-'));

			var result = BinarySearch(categoryList, category.slice(0, i + 1).join('.'), categoryComparator);
			if (result < 0) {
				result = Math.abs(result) - 1;
				categoryList.splice(result, 0, { name: category.slice(0, i + 1).join('.'), count: i + 1 == category.length ? 1 : 0 });
			}
			else if (i + 1 == category.length || categoryList[result].count > 0) {
				categoryList[result].count++;
			}

			// Add a CSS rule for showing the entry when filtering by category.
			categoryCSS.push('#WebLogWindow .category-' + category.slice(0, i + 1).join('-') + ' .category-' + category.slice(0, i + 1).join('-') + ' {display: block !important;}');
		}

		return classes;
	};

	var selectCategory = function() {
		var category = $('select', details).val();
		if (category == "") {
			$('> .entries', details).attr('class', 'entries');
		}
		else {
			$('> .entries', details).attr('class', 'hideentries entries category-' + category.replace(/\./g, '-'));
		}
	};

	var buildCategoryOptions = function() {
		var categoryLinks = [ '<option value="">All Categories</option>' ];
		for (var i = 0; i < categoryList.length; i++) {
			categoryLinks.push('<option value="' + htmlencode(categoryList[i].name) + '">' + htmlencode(categoryList[i].name) + (categoryList[i].count > 0 ? ' (' + categoryList[i].count + ')' : '') + '</span>');
		}
		$('> .categorylinks > select', details).html(categoryLinks.join(''));
		selectCategory();
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

		details.html('<div class="categorylinks">Category: <select></select></div>'
				+ '<div class="etime">Execution time: ' + etime + '</div>'
				+ '<div class="entries">'
				+ html.join("\n")
				+ '</div>');
		
		buildCategoryOptions();
		$('head').append('<style>' + categoryCSS.join("\n") + '</style>');
		renderDetails = function() {};
	};
	
	main = $('<div id="WebLogWindow" class="yui3-cssreset yui3-cssfonts yui3-cssbase"></div>')
		.hide()
		.appendTo('body');
	
	toggle = $('<div class="toggle">Log</div>')
		.toggle(function() {
			details.show();
			renderDetails();
			toggle.css({
				borderBottom: '1px solid #666',
				fontSize: '125%'
			});
			resize();
		}, function() {
			details.hide();
			toggle.css({
				borderBottom: '',
				fontSize: ''
			});
		})
		.appendTo(main);
	
	if (typeof $.fn.disableTextSelection == "function") {
		toggle.disableTextSelection();
	}

	details = $('<div class="details">')
		.hide()
		.on('click, change', 'select', function() {
			selectCategory();
		})
		.appendTo(main);

	$(window).resize(function() {
		resize();
	});
};