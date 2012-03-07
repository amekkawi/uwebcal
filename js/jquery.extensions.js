jQuery.fn.extend({
	equals: function(elem) {
		if (!elem || this.size() == 0 || (jQuery.isJQuery(elem) && elem.size() == 0)) return false;
		if (jQuery.isJQuery(elem)) elem = elem.get(0);
		
		return !(this.get(0) !== elem);
	},
	
	isParentOf: function(elem) {
		return jQuery(elem).isChildOf(this);
	},
	
	isChildOf: function(elem) {
		return this.closest(jQuery(elem)).size() > 0;
	},
	
	disableTextSelection: function() {
		return this
			.attr('unselectable', 'on')
			.css('MozUserSelect', 'none')
			.bind('selectstart.ui', function() { return false; });
	},
	
	enableTextSelection: function() {
		return this
			.attr('unselectable', 'off')
			.css('MozUserSelect', '')
			.unbind('selectstart.ui');
	}
});

jQuery.extend({
	getArrayKeys: function(arr) {
		var keys = new Array();
		if (jQuery.isObject(arr) || jQuery.isArray(arr)) {
			for (var key in arr) {
				keys.push(key);
			}
		}
		return keys;
	},
	
	isJQuery: function(obj) {
		return obj instanceof jQuery;
	},
	
	isBoolean: function(obj) {
	    return jQuery.type(obj) == "boolean";
	},
	
	isNull: function(obj) {
	    return obj === null;
	},
	
	isNumber: function(obj) {
	    return jQuery.type(obj) == 'number' && isFinite(obj);
	},
	
	isObject: function(obj) {
	    return jQuery.type(obj) == 'object' || jQuery.isFunction(obj);
	},
	
	isString: function(obj) {
	    return jQuery.type(obj) == 'string';
	},
	
	isUndefined: function(obj) {
	    return jQuery.type(obj) == 'undefined';
	},
	
	isDefined: function(obj) {
	    return !jQuery.isUndefined(obj);
	},
	
	isElement: function(object) {
		if (jQuery.isJQuery(object) && object.size() > 0) object = object.get(0);
    	return jQuery.isObject(object) && jQuery.isDefined(object.nodeType) && object.nodeType == 1;
  	},
	
	isDocumentNode: function(object) {
		if (jQuery.isJQuery(object) && object.size() > 0) object = object.get(0);
		return jQuery.isObject(object) && jQuery.isDefined(object.nodeType) && object.nodeType == 9;
	}
});