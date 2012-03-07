String.prototype.escapeId = function() {
	return escape(this)
		.replace(/\*/g, '%2A')
		.replace(/-/g, '%2D')
		.replace(/_/g, '%5F')
		.replace(/@/g, '%40')
		.replace(/\./g, '%2E')
		.replace(/\+/g, '%2B')
		.replace(/%/g, '-');
};

String.prototype.unescapeId = function() {
	return unescape(this.replace(/-/g, '%'));
};

String.prototype.htmlencode = function() {
	var div = document.createElement("div");
	div.appendChild(document.createTextNode(this));
	return div.innerHTML.replace(/"/g, "&quot;");
};

RegExp.escape = function(str) {
	var specials = new RegExp("[.*+?|()\\[\\]{}\\\\/]", "g"); // .*+?|()[]{}\/
	return str.replace(specials, "\\$&");
};

String.prototype.parseQS = function() {
	var result = {};
	var str = this.indexOf('?') == 0 ? this.substring(1) : this;
	var pairs = str.split('&');
	for (var i = 0; i < pairs.length; i++) {
		var pair = pairs[i].split('=');
		var key = unescape(pair[0]);
		var value = typeof pair[1] == "undefined" ? null : unescape(pair[1]);
		
		// Key has not yet been set.
		if ($.isUndefined(result[key])) {
			result[key] = value;
		}
		
		// Key has been set and is an array.
		else if ($.isArray(result[key])) {
			result[key][result[key].length] = value;
		}
		
		// Key has been set and is not yet an array.
		else {
			result[key] = [result[key], value];
		}
	}
	return result;
};

function BinarySearch(haystack, needle, comparator) {
	var low = 0, high = haystack.length - 1, comp = -1, mid = 0;

	if (typeof comparator != 'function') {
		comparator = function(needle, item, index) {
			if (needle < item) return -1;
			if (needle > item) return 1;
			else return 0;
		};
	}
	
	while (low <= high) {
		var mid = Math.floor((low + high) / 2);
		var comp = comparator(needle, haystack[mid], mid);
		
		if (comp < 0) {
			high = mid - 1;
		}
		else if (comp > 0) {
			low = mid + 1;
		}
		else {
			return mid;
		}
	}

	if (comp < 0) return -1 - mid;
	if (comp > 0) return -2 - mid;
}