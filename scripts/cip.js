/**
 * Grab the messages in the <script> tag and display them
 */
function cip_display() {

	$.each(cip_messages, function(index, value) {
		_cip_write(value);
	});
	
}

/**
 * Display a message directly
 */
function cip_write(message, type, options) {
	
	var my_noty = {};
	
	// add the options
	if (typeof options !== 'undefined') {
		jQuery.extend(my_noty, options);
    }
	
	// add the type
	if (typeof type !== 'undefined') {
		my_noty.type = type;
	}
	
	// add the message
	if (typeof message !== 'undefined') {
		my_noty.text = message;
	}
	
	// display the noty
    _cip_write(my_noty);
}

/**
 * Display a message directly from one message object
 */
function _cip_write(value) {
	
	value.dismissQueue = true;

	// merge with templates if one exists
	if (typeof cip_templates !== 'undefined') {

		// check if the current type has an entry in the templates
		if (value.type in cip_templates) {
			value = $.extend({}, cip_templates[value.type], value)
		}
	}
	
	// merge with defaults
	if (typeof cip_defaults !== 'undefined') {
		value = $.extend({}, cip_defaults, value)
	}

	
	if (value.layout == 'inline') {
		$('#cip').noty(value);
	} else {
		noty(value);		
	}
    
}

//previously cip_layout.js
;(function($) { // for CIP removed all css so that we can do the css in .css stylesheets, replaces bottom.js, bottomCenter.js etc.

	var cip_layout = {
		options: {},
		container: {
			style: function() {}
		},
		parent: {
			object: '<li />',
			selector: 'li',
			css: {}
		},
		css: {
			display: 'none'
		},
		addClass: ''
	};

	var layouts = ['bottom', 'bottomCenter', 'bottomLeft', 'bottomRight', 'center', 'centerLeft', 'centerRight', 'inline', 'top', 'topCenter', 'topLeft', 'topRight'];
	
	$.each(layouts, function(index, value) {
		$.noty.layouts[value] = $.extend(true, {}, cip_layout);
		$.noty.layouts[value].name = value;
		$.noty.layouts[value].container.object = '<ul id="noty_'+value+'_layout_container" />';
		$.noty.layouts[value].container.selector = 'ul#noty_'+value+'_layout_container';
	});
	
	var cip_center_layout_container_style = function(center_horizontal, center_vertical) {
		// getting hidden height
		var dupe = $(this).clone().css({visibility:"hidden", display:"block", position:"absolute", top: 0, left: 0}).attr('id', 'dupe');
		$("body").append(dupe);
		dupe.find('.i-am-closing-now').remove();
		dupe.find('li').css('display', 'block');
		var actual_height = dupe.height();
		dupe.remove();

		// prepare css
		var css = {};
		if (center_horizontal) {
			$.extend(css, {
				left: ($(window).width() - $(this).outerWidth()) / 2 + 'px'
			});
		}
		if (center_vertical) {
			$.extend(css, {
				top: ($(window).height() - actual_height) / 2 + 'px'
			});
		}
		
		if ($(this).hasClass('i-am-new')) {
			$(this).css(css);
		} else {
			$(this).animate(css, 500);
		}
	}
	
	$.noty.layouts.centerLeft.container.style   = function(){cip_center_layout_container_style.call(this, false, true)};
	$.noty.layouts.centerRight.container.style  = function(){cip_center_layout_container_style.call(this, false, true)};
	$.noty.layouts.center.container.style       = function(){cip_center_layout_container_style.call(this, true, true)};
	$.noty.layouts.topCenter.container.style    = function(){cip_center_layout_container_style.call(this, true, false)};
	$.noty.layouts.bottomCenter.container.style = function(){cip_center_layout_container_style.call(this, true, false)};

})(jQuery);

//previously cip_theme.js
;(function($) { // this is a blank team so that all styling can be done using CSS

	$.noty.themes.cip_theme = {
		name: 'cip_theme',
		helpers: {
		},
		modal: {
			css: {
			}
		},
		style: function() {
			this.$bar.bind({
				mouseenter: function() { $(this).find('.noty_close').fadeIn(); },
				mouseleave: function() { $(this).find('.noty_close').fadeOut(); }
			});
					
			this.$bar.addClass('cip_'+this.options.type);
			var selector = this.options.layout.container.selector + ' ' + this.options.layout.parent.selector;

			// add classnames to the ul
			var selector = this.options.layout.container.selector;
			$(selector).addClass('cip');
			$(selector).addClass('cip_'+this.options.layout.name);

			// add classnames to the li
			var selector = this.options.layout.container.selector + ' ' + this.options.layout.parent.selector;
			$(selector).addClass('cip_'+ (this.options.dismissQueue ? 'no_queue' : 'has_queue'));
			
			// remove cip_first from all elements but the first
			// and remove cip_last from all elements but the last
			$(selector).removeClass('cip_first cip_last');
			$(selector).first().addClass('cip_first');
			$(selector).last().addClass('cip_last');
			
			var selector = this.options.layout.container.selector + ' ' + this.options.layout.parent.selector + ' button';
			$(selector).removeClass('btn_first btn_last');
			$(selector).first().addClass('btn_first');
			$(selector).last().addClass('btn_last');
		},
		callback: {
			onShow: function() {},
			onClose: function() {}
		}
	};

})(jQuery);

// Previously stated seperately in the CIP_footer.php view
$(
	function(){
		// this way inline messages will be placed in the #cip element
		$("#cip").html('<script>cip_display();<'+'/script>');
	}
);