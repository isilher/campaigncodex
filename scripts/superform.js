var validations = new Array; //Contains all validation objects
var val_ignore_arr = new Array; //is used to tell the server which validations were removed clientside

/*****
 * Is run by the body onload attribute. Removes the "no options found" message from the suboption field but saves it so we can easily recall it
 */
function init()
{
	$('#submit').removeAttr('disabled');
}

/*****
 * Handles many-to-one form elements, creates a copy of the target element and
 * appends it to the form. Also sets livevalidation for the dynamically added
 * input fields and adds them to the form_array to make sure the handler knows
 * what to do with their post data.
 * @param i - string, the many-source div id to clone.
 */
function many(i)
{
 	num=2;
 	clones = $('div[id^="' + i + '_clone"]').each(function()
 	{
 		this_num = $(this).attr('id').replace(i + '_clone_', '');
 		if(this_num >= num)
 		{
 			num = parseInt(this_num) + 1;
 		}
 	}); 	
 	clone = $('div').find('#one_' + i).clone();

 	clone.attr('id', i + '_clone_' + num);
 	clone.attr('style', 'display: none;');
 	clone.prepend('<div class="delete_this" onclick="many_close(\'' + i + '_clone_' + num + '\')"></div>');

	var liveval = $($('#validation_script')[0]).html().replace(/(\r\n|\n|\r)/gm,"").split(';');

	var this_liveval = [];
	var is_date = [];
	var many_array_clone = [];

 	clone.find('[id^="form_"]').each(function(index) 
 	{
    	var $tag = $(this);
    	var $id = $tag.attr('id');
    	var $new_id = $id + '_' + num;
    	
		$tag.attr('id', $new_id);

		for(var j in liveval) 
		{
			var value = liveval[j];
			var value_name = value.split('=')[0].split('.')[0].replace(/ /gm, '').replace('var', '');
			if(value_name == $id)
			{
				this_liveval.push(value.replace( new RegExp($id,'g'), $new_id));
			}
		}

		$name = $tag.attr('name');
    	$pos = $name.lastIndexOf('[');
    	var newName = '';
    	if ($pos==-1) {
    		$newName = i + '_many_' + $name + "_" + num;
    	} else {
    		$newName = i + '_many_' + $name.substr(0, $pos) + "_" + num + '[]';
    	}

    	$tag.attr('name', $newName);
		$tag.attr('value', '');
		var dp = "hasDatepicker";
	 	if($tag.hasClass(dp))
	 	{
	 		is_date.push($tag.attr('id'));
	 	}
	 	$tag.removeClass('hasDatepicker');
	 	$tag.removeClass('LV_invalid_field');
	 	$tag.removeClass('LV_valid_field');
	 	
	 	for(var y in form_array)
		{
			var val = form_array[y];

			if(val.field == 'many_open_' + i)
			{
				if($.inArray(val, many_array_clone) === -1)
				{
					many_array_clone.push(val);
				}
			}

			if(val.field == $name || val.field + '[]' == $name) 
			{
				var clone_obj = jQuery.extend(true, {}, val);
				clone_obj.field = i + '_many_' + val.field + "_" + num;
				many_array_clone.push(clone_obj);
			}			
		}
		
	});

	var splicer = 0;

	for(var y in form_array)
	{
		var val = form_array[y];
		if(val.field == 'many_close_' + i)
		{
			splicer = (y * 1) + 1;

			if($.inArray(val, many_array_clone) === -1)
			{
				many_array_clone.push(val);
			}
		}
	}

	for(var z in many_array_clone)
	{
		var val = many_array_clone[z];
		form_array.splice(splicer + (z * 1), 0, val);
	}

	update_form_array();

 	clone.find('span').remove();
	clone.appendTo('#many_' + i).slideDown('fast', 'swing');

	if(is_date)
	{
		for(var dp in is_date)
		{
			add_date_picker(is_date[dp]);
		}
	}
	
	for(var j in this_liveval) 
	{
		var value = this_liveval[j];

		if(value.indexOf("var") != -1)
		{
			obj = value.substr(value.indexOf("var ")+4);
			name = obj.substring(0, obj.indexOf(" "));
			rest = obj.substring(obj.indexOf(" "));
			value = "window['" + name + "']" + rest;
		}else
		{
			value = "window." + value;	
		}	

		eval(value);
	}
	
 }

/*****
 * Handles many-to-one form element clones, removing them from the
 * current form and the form_array. 
 * @param me - string, the clone div id to remove.
 */
function many_close(me)
{
	junk = $('div[id*="' + me + '"]');
 	junk.each(function(){
 		var clone = $(this);
	 	clone.addClass('duplicant-delete');
	 	var i = 0;
	 	var el_name = '';

	 	clone.find('[id*="form_"]').each(function(index, element){
	 		var el_id = $(this).attr('id');
	 		el_name = $(this).attr('name');
	 		el_name = el_name.replace('[', '');
	 		el_name = el_name.replace(']', '');
	 		i += 1;

	 		if(window[el_id].elementType != undefined)
	 		{
	 			window[el_id].destroy();
	 			ignore_validation(el_name, 'remove');
	 		}
	 		
		});
	 	
		for(var z in form_array)
	 	{
	 		if(form_array[z].field === el_name)
	 		{
	 			// remove all entries from the form_array starting with the 
	 			// cloned many_open Object (last element found minus i, the total 
	 			// number of cloned elements), splicing the total number of cloned
 				// elements, plus 2 (for the many_open and many_close Objects)
	 			form_array.splice(z-i, i+2);
	 		}
	 	}
	 	
 		update_form_array();

	 	// remove DOM elements
	 	clone.fadeOut('slow', function(){clone.remove()});
	});
}

function vali_date(input_id)
{
 	eval(input_id).validate();
}

function add_date_picker(input_id)
{
 	var currentElement = false;

 	$('#' + input_id).select(function(){currentElement = this});

 	$('#' + input_id).datepicker({
 		minDate: 0,
 		dateFormat: "dd-mm-yy",
	 	onClose: function(){
	 		vali_date(input_id);
	 	}
 	});
}

/*****
 * Sets the language of every DatePicker element to Dutch
 */
function dutch_datepickers(){
	var regional = {
		closeText: 'Sluiten',
		prevText: 'Vorige',
		nextText: 'Volgende',
		currentText: 'Vandaag',
		monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
		'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
		monthNamesShort: ['jan', 'feb', 'maa', 'apr', 'mei', 'jun',
		'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
		dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
		dayNamesShort: ['zon', 'maa', 'din', 'woe', 'don', 'vri', 'zat'],
		dayNamesMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
		weekHeader: 'Wk',
		dateFormat: 'dd-mm-yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults(regional);
};

/*****
 * Updates a time field with the corresponding select elements
 */
function time_counter(field)
{
	var field_name = $(field)[0].name;
	var hours = eval(field_name + "_hours").value;
	var minutes = eval(field_name + "_minutes").value;
	field.value = hours + ":" + minutes;
}

/*****
 * Set a maximum interval between two DatePicker elements
 * will set a max_date for input_b when input_a is defined
 * will forcibly change the value of input_b if an illegal date was chosen 
 * before input_a was defined.
 */
function set_date_interval(input_a, input_b, max_interval)
{
	var start_date = $('input#' + input_a).attr('value');
	var end_date = $('#' + input_b).attr('value');
	
	if(end_date === '')
	{
		end_date = start_date.split('-');
		$('#' + input_b).attr('value', start_date);
	}
	else
	{
		end_date = end_date.split('-');
	}

	var min_date = start_date;
	start_date = start_date.split('-');
	start_date = new Date(start_date[2], start_date[1], start_date[0]);	
	end_date = new Date(end_date[2], end_date[1], end_date[0]);
	var ONE_DAY = 1000 * 60 * 60 * 24;
	var difference = Math.abs(Math.round((end_date - start_date)/ONE_DAY));
		
	var max_date = date_zero(start_date.getDate() + max_interval) + '-' + date_zero(start_date.getMonth()) + '-' + start_date.getFullYear();

	if(difference > max_interval)
	{
		$('#' + input_b).attr('value', max_date);
	}

	$('#' + input_b).datepicker( "option", "minDate", min_date );
	$('#' + input_b).datepicker( "option", "maxDate", max_date );
}

/*****
 * A little helper function to change date integers to a a string in the desired format
 * of two digits ( ie 07 rather than 7 )
 */
function date_zero(date_element)
{
	
	if(date_element < 10)
	{
		return '0' + date_element;
	}

	return date_element;
}

/*****
 * Add or remove an element_id to the val_ignore_arr array. This array will be sent along with
 * the POST to tell the handler to skip server-side validation on these elements.
 */
function ignore_validation(element_id, remove)
{
	if($('input#ignore_validations').length === 0)
	{
		$('form').append('<input type="hidden" name="ignore_validations" id="ignore_validations">');
	}

	if(remove === 'remove')
	{
		if($.inArray(element_id, val_ignore_arr) !== -1)
		{
			val_ignore_arr.splice( $.inArray(element_id, val_ignore_arr), 1 );
		}
	}
	else
	{
		if($.inArray(element_id, val_ignore_arr) === -1)
		{
			val_ignore_arr.push(element_id);
		}
	}

	$('input#ignore_validations').attr('value', val_ignore_arr);
}

/*****
 * Updates the form_array hidden field so that it can hand relevant form information
 * back to the handler controller.
 */
function update_form_array()
{
	var hidden_array = $('input[name=form_array]');

	// only if the input exists
	if (hidden_array.length >0)
	{
		var arr = [];

		for(var y in form_array)
		{
			var val = form_array[y];
			arr.push(JSON.stringify(val));
		}

		hidden_array.get(0).value = arr;
	}
}

/*****
 * Run when the document is ready
 */
$(document).ready(function(){

	// add DatePicker elements to each DatePicker field
	$('input.datepicker').each(function(){
		add_date_picker(this.id);
	});

	// if the current display language is Dutch, set the DatePickers to Dutch
	// if( <<dutch language boolean>> )
	// {
	//  	dutch_datepickers();
	// }

	// Synchronise form_array with it's corresponding form element,
	// but only if we have one
	if (typeof form_array!="undefined")
	{
		update_form_array();
	}

});
