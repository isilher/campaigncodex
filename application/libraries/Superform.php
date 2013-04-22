<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Super Extensible Live Validating Form Generating Library (SELVFGL aka Superform)
 *
 * Combines the Livevalidation class and the Form Generation class from Frank Michel (http://www.frankmichel.com/)
 * to create a very powerful tool to create and validate (both client- and server side) forms
 *
 * @package Superform
 * @version 1.0
 * @author Bram Couteaux bramcou@gmail.com
 * @author Rik van Duijn r.vanduijn@uu.nl
 *
 * @param array used to store custom field specific error messages, key => field, value => the error message
 *              this is done because the livevalidation class isn't invoked until get() is run and we want to be able
 *				to set error messages earlier on
 */ 
 
class Superform extends Form
{
	private $custom_error_messages = array();
	
	/**
	 * Add (Element), overriden to have Superform use Superel instead of default El
	 * 
	 * Instantiates element object
	 *
	 * @return object the Superform object for method chaining
	 */
	public function add($info) 
	{
		$el = new Superel($info, $this->config);
		$this->_add_element_to_form($el);
		return $this;
	}

	/**
	 * Get calls Form get() and than additionally instantiates a Livevalidation object for the form and creates the required code
	 * that can then be printed by get_js()
	 *
	 * @return string the form string
	 */
	public function get()
	{
		$ignore_els = explode(',', $this->CI->input->post('ignore_validations'));
		$diffs = array();

		foreach($this->_elements as $key => $el)
		{
			if( !in_array($el['unique'], $ignore_els) )
			{
				$diffs[] = $el;
			}
		}

		$this->_elements = $diffs;

		//get the form string
		$form = parent::get();

		$this->CI->load->library('livevalidation');

		// set the livevalidation to only trigger on Blur, to prevent agressive premature errors
		$this->CI->livevalidation->set_field_defaults(array('onlyOnBlur' => TRUE));

		//set clientside validation for all elements
		foreach($this->_elements as $el)
		{
			if(isset($el['unique']))
			{
				$element = $el['unique'];

				//only proceed if there are rules set
				if(isset($this->$element->rules))
				{
					//create a new field object, check for any default params and add the field_id
					$params = array('field_id' => $el['unique']);
					$params = array_merge($this->config['livevalidation_default_params'], $params);
					$field = $this->CI->livevalidation->create_field($params);

					//only proceed if the element type is one supported by livevalidation
					if(in_array($this->$element->type, $field->get_supported_element_types()) == false)
					{
						$this->CI->livevalidation->delete_field($element);
						continue;
					}
					
					$rules = array();
					$rule = $this->$element->rules;
					$ignorepipes = false;
					$current = 0;
					for($i = 0; $i < strlen($rule); $i++)
					{
						//check if we'd like to ignore pipes
						if(substr($rule, $i, 1) == '~')
						{
							$ignorepipes = true;
						}
						//check if we'd like to stop ignoring pipes
						elseif(substr($rule, $i, 1) == '`')
						{
							$ignorepipes = false;
						}
						//detect a not-to-be-ignored pipe
						elseif(substr($rule, $i, 1) == '|' && $ignorepipes == false)
						{
							//make sure we grab out the ~ and `
							$rules[] = substr($rule, $current, $i - $current);
							$current = $i + 1;
						}
					}
					$rule_dirty = substr($rule, $current);
					$rules[] = str_replace('~', '', str_replace('`', '', $rule_dirty));
					
					//only proceed if the element type is one supported by livevalidation
					$pass_over = true;
					foreach($rules as $rule)
					{
						if(stripos($rule, '[') !== false)
						{
							$rule = substr($rule, 0, stripos($rule, '['));
						}
						
						//match means we dont pass it over as their is a livevalidation supported rule
						if(array_key_exists($rule, $this->config['rule_translations']))
						{
							$pass_over = false;
						}
					}
					
					//if there isn't a single rule that is supported by livevalidation there isn't much point of using it, is there?
					if($pass_over)
					{
						$this->CI->livevalidation->delete_field($element);
						continue;
					}

					//add the rules
					foreach($rules as $rule)
					{
						$rule = str_replace('~', '', $rule);
						if(!empty($rule))
						{
							//only proceed if the given rule is available for Livevalidation
							if(array_key_exists($rule, $this->config['rule_translations']) || array_key_exists(substr($rule, 0, stripos($rule, '[')), $this->config['rule_translations']))
							{
								//check for possible parameter
								$param = '';
								if(stripos($rule, '[') !== false && substr($rule, -1) == ']')
								{
									$temp = $rule;
									$rule = substr($rule, 0, stripos($rule, '['));
									$param = substr($temp, stripos($temp, '[') + 1, -1);
								}

								$params = array();
								if(isset($this->config['rule_translations'][$rule]['param']))
								{
									//if it's not an array yet make one
									if(!is_array($this->config['rule_translations'][$rule]['param']))
									{
										$this->config['rule_translations'][$rule]['param'] = array($this->config['rule_translations'][$rule]['param'] => '');
									}
									
									//for each parameter we add it to the params array
									$first = true;
									foreach($this->config['rule_translations'][$rule]['param'] as $param_name => $value)
									{
										if($value == '')
										{
											$value = 'true';
										}
										
										//if this is the first param and their has been passed a value in the rules
										if($first && $param != '')
										{
											$params[$param_name] = $param;
										}
										else
										{
											$params[$param_name] = $value;
										}
										
										$first = false;
									}
								}
								
								//is there a custom error message set, then we set it for all possible error messages from Livevalidation
								if(isset($this->custom_error_messages[$el['unique']]))
								{
									$error_params = $this->config['rule_translations'][$rule]['error_params'];
									if(is_array($error_params) && !empty($error_params))
									{
										foreach($error_params as $param)
										{
											$params[$param] = $this->custom_error_messages[$el['unique']];
										}
									}
								}
								
								//pass the rule and parameters to Livevalidation
								$field->add($this->config['rule_translations'][$rule]['name'], $params);
							}
						}
					}
				}
			}
		}

		return $form;
	}
	
	/**
	 * Get JS code
	 * 
	 * @return string the javascript code for the Livevalidation but only if get() has been run, false otherwise
	 */
	public function get_js()
	{
		if(is_object($this->CI->livevalidation))
		{
			return $this->CI->livevalidation->print_js();
		}
		
		return false;
	}
	
	/**
	 * Get POST data,
	 * makes a checkbox say 'checked' or 'unchecked' instead of an array with one empty value
	 * 
	 * @return array the post data
	 */
	public function get_post($xss_clean = false)
	{
		$post = parent::get_post($xss_clean);

		foreach($this->_elements as $el)
		{
			if($el['type'] == 'checkbox')
			{
				$name = str_replace('[]', '', $el['name']);
				
				if(is_array($post[$name]))
				{
					$post[$name] = 'checked';
				}
				else
				{
					$post[$name] = 'unchecked';
				}
			}
		}

		return $this->strip_tags_array($post);
	}

	
	/**
	 * Strip al PHP and HTML tags from all entries in an array or a multidimensional array.
	 * 
	 * @param array the array holding (arrays holding) strings to be stripped of HTML and PHP tags
	 * @param string (optional) a string containing all the HTML and/or PHP tags you wish to exclude from being stripped
	 * @return the stripped array
	 */	
	public function strip_tags_array($data, $tags = null)
	{
		$stripped_data = array();
		$this->CI->load->helper('charconv');
		foreach ($data as $key=>$value)
		{
			if (is_array($value))
			{
				$stripped_data[$key] = $this->strip_tags_array($value, $tags);
			}
			else
			{
				$stripped_data[$key] = strip_tags($value, $tags);
			}
		}
		return $stripped_data;
	}
	
	/**
	 * Get an element object
	 * 
	 * @param string the name of the element, if left empty tries to grab the last accessed element
	 * @return the element reference or false on failure
	 */	
	public function get_el($name = '')
	{
		$el = false;
		
		//search the element (if it exists)
		if($name != '')
		{
			$el = $this->_el_get_unique($name);		
		}
		elseif($this->_last_accessed && $name == '') 
		{
			$el = $this->_last_accessed;
		}
		
		return $el;
	}
	
	/**
	 * Clear all rules from an element both in the rules property and in the rules key from the atts array
	 * 
	 * @param string the name of the element, if left empty tries to grab the last accessed element
	 * @return boolean true on success, false on error
	 */	
	public function clear_rules($name = '')
	{
		$el = $this->get_el($name);
		
		//remove the rules from the element
		if($el) 
		{
			$this->$el->rules = '';
			$this->$el->atts['rules'] = '';
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Set a custom error message in Form class and save it here so it will be grabbed for the clientside validation when get() is run
	 * 
	 * @param string the field the message applies to
	 * @param string the message
	 */	
	public function set_error($fieldunique, $message)
	{
		//server-side
		parent::set_error($fieldunique, $message);
		
		//prep for client-side
		$this->custom_error_messages[$fieldunique] = $message;
		
		return $this;
	}
	
	/**
	 * Extend native FormGen text() method to include error messages and hints in a more suitable argument order
	 *
	 * @param string the name and id seperated by a | or if $config['nameid'] = true it is both the name and id
	 * @param string the element label
	 * @param string the validation rules seperated by a |
	 * @param string default value
	 * @param string hint (shown with icon behind field)
	 * @param string error message to be shown when validation fails
	 * @param array  additional html attributes
	 *
	 * @return object the Superform object for method chaining
	 */
	public function text($nameid, $label = '', $rules = '', $value = '', $hint = '', $errormsg = '', $atts = array())
	{	
		parent::text($nameid, $label, $rules, $value, $atts);
		
		//get the element
		$el = $this->get_el($this->_last_accessed);
			
		//set error msg
		if($errormsg != '')
		{
			$this->set_error($this->$el->unique, $errormsg);
		}
		
		//set hint
		if($hint != '')
		{
			$this->$el->hint($hint);
		}
		
		return $this;
	}
	
	/**
	 * Extend native FormGen textarea() method to include error messages and hints in a more suitable argument order
	 *
	 * @param string the name and id seperated by a | or if $config['nameid'] = true it is both the name and id
	 * @param string the element label
	 * @param string the validation rules seperated by a |
	 * @param string default value
	 * @param string hint (shown with icon behind field)
	 * @param string error message to be shown when validation fails	 
	 * @param array  additional html attributes
	 *
	 * @return object the Superform object for method chaining
	 */
	public function textarea($nameid, $label = '', $rules = '', $value = '', $hint = '', $errormsg = '', $atts = array())
	{
		parent::textarea($nameid, $label, $rules, $value, $atts);
		
		//get the element
		$el = $this->get_el($this->_last_accessed);
			
		//set error msg
		if($errormsg != '')
		{
			$this->set_error($this->$el->unique, $errormsg);
		}
		
		//set hint
		if($hint != '')
		{
			$this->$el->hint($hint);
		}
		
		return $this;
	}
	
	/**
	 * Extend native FormGen textarea() method to include error messages and hints in a more suitable argument order
	 *
	 * @param string the name and id seperated by a | or if $config['nameid'] = true it is both the name and id
	 * @param string the element label
	 * @param string the validation rules seperated by a |
	 * @param string default value
	 * @param string hint (shown with icon behind field)
	 * @param string error message to be shown when validation fails	 
	 * @param array  additional html attributes
	 *
	 * @return object the Superform object for method chaining
	 */
	public function password($nameid, $label = '', $rules = '', $value = '', $hint = '', $errormsg = '', $atts=array())
	{
		parent::password($nameid, $label, $rules, $value, $atts);
		
		//get the element
		$el = $this->get_el($this->_last_accessed);
			
		//set error msg
		if($errormsg != '')
		{
			$this->set_error($this->$el->unique, $errormsg);
		}
		
		//set hint
		if($hint != '')
		{
			$this->$el->hint($hint);
		}
		
		return $this;
	}
	
	/**
	 * Extend native FormGen hidden() method to include error messages and hints in a more suitable argument order
	 *
	 * @param string the name and id seperated by a | or if $config['nameid'] = true it is both the name and id
	 * @param string the default value onload
	 * @param string the validation rules seperated by a |
	 * @param string error message to be shown when validation fails	 
	 *
	 * @return object the Superform object for method chaining
	 */
	public function hidden($nameid, $value = '', $rules = '', $errormsg = '')
	{
		parent::hidden($nameid, $value, $rules);
		
		//get the element
		$el = $this->get_el($this->_last_accessed);
			
		//set error msg
		if($errormsg != '')
		{
			$this->set_error($this->$el->unique, $errormsg);
		}
		
		return $this;
	}
	
	/**
	 * Extend native FormGen checkbox() method to include error messages and hints in a more suitable argument order
	 *
	 * @param string the name and id seperated by a | or if $config['nameid'] = true it is both the name and id
	 * @param string the element label
	 * @param string the validation rules seperated by a |
	 * @param string default value
	 * @param string hint (shown with icon behind field)
	 * @param string error message to be shown when validation fails	 
	 * @param array  additional html attributes
	 * @param bool   whether the checkbox is default checked
	 *
	 * @return object the Superform object for method chaining
	 */
	public function checkbox($nameid, $label = '', $rules = '', $value = '', $hint = '', $errormsg = '', $atts = array(), $checked = false)
	{
		parent::checkbox($nameid, $value, $label, $checked, $rules, $atts);
		
		//get the element
		$el = $this->get_el($this->_last_accessed);
			
		//set error msg
		if($errormsg != '')
		{
			$this->set_error($this->$el->unique, $errormsg);
		}
		
		//set hint
		if($hint != '')
		{
			$this->$el->hint($hint);
		}
		
		return $this;
	}
	
	/**
	 * Extend native FormGen checkgroup() method to include error messages
	 *
	 * @param string the name and id seperated by a | or if $config['nameid'] = true it is both the name and id
	 * @param array  the array with all the checkboxes (see FormGen lib for detailed array structure explanation)
	 * @param string the element label
	 * @param string error message to be shown when validation fails
	 * @param string checked checkboxes
	 * @param string the validation rules seperated by a |	 
	 * @param array  additional html attributes
	 *
	 * @return object the Superform object for method chaining
	 */
	public function checkgroup($name, $checks = array(), $label = '', $errormsg = '', $checked = array(), $rules = '', $atts = array())
	{
		$checked = $this->_make_array($checked);
		$atts = $this->_make_info($atts);
		$this->_check_name($name, 'checkgroup');
		if ( ! count($checks)) show_error(FGL_ERR.'You did not supply an array with the checkboxes for element: '.$name);

		$i = 0;
		$this->_last_accessed_group = TRUE;

		$label_pos = (isset($this->config['defaults']['label']['position'])) ? $this->config['defaults']['label']['position'] : 'before';
		$label_pos = (isset($this->config['defaults']['checkgroup']['label']['position'])) ? $this->config['defaults']['checkgroup']['label']['position'] : $label_pos;

		if ($label && $label_pos == 'before') $this->label($label, '', $atts);
		foreach ($checks as $check)
		{
			$info = array();
			
			// get attributes
			if (array_key_exists(2, $check)) 
			{
				// get element attributes
				$info = $this->_make_info($check[2]);

				// get label attributes
				$info['label'] = $this->_make_info($check[2], 'label');
			}

			$i++;
			$this->_make_valueid($check[0], $info);
			
			$info['type'] = 'checkbox';
			$info['name'] = $name;
			$info['checked'] = (in_array($info['value'], $checked)) ? TRUE : FALSE;
			$info['label_text'] = $check[1];
			$info['rules'] = $rules;
			$info['group_label'] = $label;
			if ($i == 1) $info['group'] = 'first';
			if ($i == count($checks)) $info['group'] = 'last';
			$this->add($info);
			
			//get the element
			$el = $this->get_el($this->_last_accessed);
			
			//set error msg
			if($errormsg != '')
			{
				$this->set_error($this->$el->unique, $errormsg);
			}
		}
		if ($label && $label_pos == 'after') $this->label($label, '', $atts);
		$this->_last_accessed_group = FALSE;

		return $this;
	}
	
	/**
	 * Extend native FormGen radiogroup() method to include error messages and hints in a more suitable argument order
	 *
	 * @param string the name and id seperated by a | or if $config['nameid'] = true it is both the name and id
	 * @param array  the radiobutton in label/value pairs
	 * @param string the element label
	 * @param string the validation rules seperated by a |
	 * @param string hint (shown with icon behind field)
	 * @param string error message to be shown when validation fails	 
	 * @param array  additional html attributes
	 * @param array  which radiobutton is checked onload
	 *
	 * @return object the Superform object for method chaining
	 */
	public function radiogroup($name, $radios = array(), $label = '', $rules = '', $hint = '', $errormsg = '', $atts = array(), $checked = array())
	{
		parent::radiogroup($name, $radios, $label, $checked, $rules, $atts);
		
		//get the element
		$el = $this->get_el($this->_last_accessed);
		
		return $this;
	}
	
	/**
	 * Extend native FormGen select() method to include error messages and hints in a more suitable argument order
	 *
	 * @param string the name and id seperated by a | or if $config['nameid'] = true it is both the name and id
	 * @param array  the options in name/value pairs
	 * @param string the element label
	 * @param string the validation rules seperated by a |
	 * @param string hint (shown with icon behind field)
	 * @param string error message to be shown when validation fails	 
	 * @param array  additional html attributes
	 * @param array  which option is selected onload
	 *
	 * @return object the Superform object for method chaining
	 */
	public function select($nameid, $options = array(), $label = '', $rules = '', $hint = '', $errormsg = '', $atts = array(), $selected = '')
	{
		parent::select($nameid, $options, $label, $selected, $rules, $atts);
		
		//get the element
		$el = $this->get_el($this->_last_accessed);
			
		//set error msg
		if($errormsg != '')
		{
			$this->set_error($this->$el->unique, $errormsg);
		}
		
		//set hint
		if($hint != '')
		{
			$this->$el->hint($hint);
		}
		
		return $this;
	}
	
	/**
	 * Extend native FormGen upload() method to include error messages and hints in a more suitable argument order
	 *
	 * @param string the name and id seperated by a | or if $config['nameid'] = true it is both the name and id
	 * @param string the element label
	 * @param bool   whether the file upload is required
	 * @param string hint (shown with icon behind field)
	 * @param string error message to be shown when validation fails	 
	 * @param array  additional html attributes
	 *
	 * @return object the Superform object for method chaining
	 */
	public function upload($nameid, $label = '', $required = false, $atts = array(), $hint = '', $errormsg = '')
	{
		parent::upload($nameid, $label, $required, $atts);
		
		//get the element
		$el = $this->get_el($this->_last_accessed);
			
		//set error msg
		if($errormsg != '')
		{
			$this->set_error($this->$el->unique, $errormsg);
		}
		
		//set hint
		if($hint != '')
		{
			$this->$el->hint($hint);
		}
		
		return $this;
	}
	
	/**
	 * Extends default submit to enable autoloading the value of it
	 *
	 * @param string the value (displayed name) of the element, if left empty we will attempt to get the value from the lang file
	 * @param string nameid
	 * @param array  additional attributes
	 *
	 * @return object the Superform object for method chaining
	 */
	public function submit($value = 'Submit', $nameid = 'submit', $atts = array())
	{
		parent::hidden('form_array');

		$line = $this->CI->lang->line('form_submit');
		if($value == 'Submit' && !empty($line))
		{
			$value = $line;
		}

		parent::submit($value, $nameid, $atts);
		
		return $this;
	}
	
	/**
	 * Extends default reset to enable autoloading the value of it
	 *
	 * @param string the value (displayed name) of the element, if left empty we will attempt to get the value from the lang file
	 * @param string nameid
	 * @param array  additional attributes
	 *
	 * @return object the Superform object for method chaining
	 */
	public function reset($value = 'Reset', $nameid = 'reset', $atts = array())
	{
		$line = $this->CI->lang->line('form_reset');
		if($value == 'Reset' && !empty($line))
		{
			$value = $line;
		}

		foreach ($this->_names as $key => $name_value) 
		{
			if (strpos($name_value, "many_open") !== FALSE) 
			{
				$atts = array('onclick' => 'many_close(\'clone\')');
				break;
			}
		}
		
		parent::reset($value, $nameid, $atts);
		
		return $this;
	}
	
	/**
	 * Upload Files, extended to support custom error messages
	 * 
	 * Uploads files
	 */		
	function _upload_files() 
	{
		foreach ($this->_files as $el)
		{
			$config = array(
				'upload_path' => $this->$el->upload_path,
				'allowed_types' => $this->$el->allowed_types,
				'file_name' => $this->$el->file_name,
				'overwrite' => $this->$el->overwrite,
				'max_size' => $this->$el->max_size,
				'max_width' => $this->$el->max_width,
				'max_height' => $this->$el->max_height,
				'max_filename' => $this->$el->max_filename,
				'encrypt_name' => $this->$el->encrypt_name,
				'remove_spaces' => $this->$el->remove_spaces
			);

			$this->CI->load->library('upload');
			$this->CI->upload->initialize($config);
			$this->CI->lang->load('upload', $this->lang);
			
			if ( ! $this->CI->upload->do_upload($this->$el->name))
			{
				$error = $this->CI->upload->display_errors('','');

				if ( ! $this->$el->required && $error == $this->CI->lang->line('upload_no_file_selected')) 
				{
					// file not required and no file selected
				} 
				else 
				{
					// add error
					$this->error_array[$el] = $this->$el->err_message;
					$this->valid = FALSE;
				}
			} 
			else 
			{
				$this->_data[$this->$el->name] = $this->CI->upload->data();
			}		
		}
	}

	/**
	 * Many to one relation form fields to cooperate with JS many()
	 * builds html code to preceed elements that need to be duplicated
	 * client-side. Always close the Many set with the many_close()
	 * @param string title that will be printed as the many-group header
	 * @return object the Superform object for method chaining.
	 * @todo see if we can cut back on empty variables for cleaner code
	 */
	public function many_open($title = 'Titel van de many div')
	{
		$nameid = 'many_open_';
		$label = '';
		$rules = ''; 
		$value = ''; 
		$atts = array();

		$i = 1;

		foreach ($this->_names as $key => $value) 
		{
			if (strpos($value, $nameid) !== FALSE) 
			{
				$i = $i + 1;
			}
		}

		$nameid = $nameid . $i;

		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name'], 'many_open');

		$info['type'] = 'many_open';
			//TODO: Kijken of we ene label en label_text en rules en value mee moeten geven of niet
		$info['label'] = $this->_make_info($atts, 'label');
		$info['label_text'] = $label;
		$info['rules'] = $rules;
		$info['value'] = $value;
		$info['title'] = $title;
		$info['html'] = '<div id="many_' . $i . '" class="many_div"><div id="one_' . $i . '" class="duplicant"><h3>' . $title . '</h3>';	
		$this->add($info);

		return $this;
	}

	/**
	 * Many to one relation form fields to cooperate with JS many()
	 * builds html code to close group of elements-group created with the many_open element.
	 *
	 * when receiving post data this function also adds any added clone fields as SuperEl elements
	 *
	 * @param string button text that will be printed on the add-button
	 * @return object the Superform object for method chaining.
	 */
	public function many_close($button = '+')
	{
		$last_open = 1;
		$closed = array();
		$nameid = 'many_close_';

		foreach ($this->_names as $key => $value) 
		{
			if (strpos($value, 'many_open_') !== FALSE) 
			{
				$last_open = str_replace('many_open_', '', $value);
			}

			if (strpos($value, 'many_close_') !== FALSE) 
			{
				$closed[] = str_replace('many_close_', '', $value);
			}
		}

		while($last_open > 0)
		{
			if(in_array($last_open, $closed) === FALSE)
			{
				break;
			}
			$last_open = $last_open - 1;
		}

		$nameid = $nameid . $last_open;
		$last_clone  = 0;

		$post = $this->CI->input->post();

		if(!$post)
		{
			$post = array();
		}
		
		foreach($post as $key => $value)
		{
			$pos = strpos($key,'many_form');
			if($pos !== false) 
			{
			 	$parent = str_replace('many_', '', strstr($key, 'many_'));

				$in_many = str_replace('_', '', str_replace(strstr($key, 'many_'), '', $key));

			 	if ($in_many == $last_open) 
			 	{
				 	$pos = strrpos($parent, '_');
				 	$num = $pos - strlen($parent);
				 	$clone_id = substr($parent, $num);
				 	$parent = substr($parent, 0, $pos);
				 	$id = $last_open . '_clone' . $clone_id;
				 	
					if ($last_clone != $id) 
				 	{
				 		$my_open = 'many_open_' . $last_open;
				 		$title = $this->$my_open->title;
				 		$this->html('</div><div id="' . $id . '" class="duplicant"><div class="delete_this" onclick="many_close(\'' . $id . '\')"></div><h3>' . $title . '</h3>');
				 		$last_clone = $id;
				 	}
				 	
				 	$clone_atts = $this->$parent->atts;

				 	$info = $this->_make_info($clone_atts);
				 	$this->_make_nameid($key, $info);
				 	$this->_check_name($info['name'], $key);
				 	$info['unique'] = $key;

				 	$this->add($info);
				 }
			}
		}
		
		$label = '';
		$rules = ''; 
		$value = ''; 
		$atts = array();

		$info = $this->_make_info($atts);
		$this->_make_nameid($nameid, $info);
		$this->_check_name($info['name'], 'many_close');

		$info['type'] = 'many_close';
		$info['label'] = $this->_make_info($atts, 'label');
		$info['label_text'] = $label;
		$info['rules'] = $rules;
		$info['value'] = $value;
		$info['html'] = '</div></div><div id="add_more"><span class="add_more_button_icon"></span><button type="button" class="add_more_button" onclick="many(' . $last_open . ');">' . $button . '</button></div><br />';	
		$this->add($info);

		return $this;
	}


	/**
	 * Adds the time() method to the forum building functionality
	 * includes error messages and hints
	 *
	 * @param string the name and id seperated by a | or if $config['nameid'] = true it is both the name and id
	 * @param array  the hours to be displayed in the hour dropdown
	 * @param array  the minutes to be displayed in the minutes dropdown
	 * @param string the element label
	 * @param string the validation rules seperated by a |
	 * @param string default value
	 * @param string hint (shown with icon behind field)
	 * @param string error message to be shown when validation fails
	 * @param array  additional html attributes
	 *
	 * @return object the Superform object for method chaining
	 */
	public function time($nameid, $hours_options = '', $minutes_options = '', $label = '', $rules = '', $value = '', $hint = '', $errormsg = '', $atts = array())
	{	
		$text_atts = array('element_suffix' => '', 'class' => 'hidden_time') + $atts;

		if($hours_options == '')
		{
			$hours_options = array('00' => '00',
								'01' => '01',
								'02' => '02',
								'03' => '03',
								'04' => '04',
								'05' => '05',
								'06' => '06',
								'07' => '07',
								'08' => '08',
								'09' => '09',
								'10' => '10',
								'11' => '11',
								'12' => '12',
								'13' => '13',
								'14' => '14',
								'15' => '15',
								'16' => '16',
								'17' => '17',
								'18' => '18',
								'19' => '19',
								'20' => '20',
								'21' => '21',
								'22' => '22',
								'23' => '23',								
								);
		}

		if($minutes_options == '')
		{
			$minutes_options = array('00' => '00',
								'15' => '15',
								'30' => '30',
								'45' => '45',							
								);
		}

		parent::text($nameid, $label, $rules, $value, $text_atts);

		//get the element
		$el = $this->get_el($this->_last_accessed);
			
		//set error msg
		if($errormsg != '')
		{
			$this->set_error($this->$el->unique, $errormsg);
		}

		$hours = $nameid . "_hours";
		$minutes = $nameid . "_minutes";

		$js = $nameid . '.value = ' . $hours . '1.value' . " + ':' + " . $minutes . '1.value' . " + ' - ' + " . $hours . '2.value' . " + ':' + " . $minutes . '2.value' . ';';
		
		if(array_key_exists('singletime', $atts))
		{
			$js = $nameid . '.value = ' . $hours . '2.value' . " + ':' + " . $minutes . '2.value' . ';';
		}

		$mid_atts = array('element_suffix' => '', 'class' => 'time_select', 'onchange' => $js) + $atts;
		$last_atts = array('class' => 'time_select', 'onchange' => $js) + $atts;

		if(!array_key_exists('singletime', $atts))
		{
			parent::select($hours . '1', $hours_options, '', '', '', $mid_atts);
			parent::select($minutes . '1', $minutes_options, '', '', '', $mid_atts);
			parent::html(' <label for="form_time_hours2" style="width:10px;">&nbsp&nbsp-&nbsp</label> ');
		}
		parent::select($hours . '2', $hours_options, '', '', '', $mid_atts);
		parent::select($minutes. '2', $minutes_options, '', '', '', $last_atts);
		
		
		//get the element
		$el = $this->get_el($this->_last_accessed);
			
		//set hint
		if($hint != '')
		{
			$this->$el->hint($hint);
		}
		
		return $this;
	}
}


/**
 * Superel (SuperEl) extends El
 *
 * @package Superform
 * @param string can contain a hint that is showed when mousing over an questionmark img behind the field
 */ 
class Superel extends El
{	
	private $hint;
	
	/**
	 * Adds extra html behind any element, this function is called in a loop structure by _elements_to_string from
	 * the Form class
	 */ 
	public function get()
	{
		$output = parent::get();
		
		//if their's a hint set we put it directly after the element
		if(!empty($this->hint) && isset($this->hint_img_url))
		{
			//check if there's an element suffix, we wanna put the hint img before that
			$substringged = false;
			if(!empty($this->element_suffix))
			{
				$substringged = true;
				$output = substr($output, 0, -strlen($this->element_suffix));
			}
			
			//add the hint
			$output .= '<img src="' . base_url() . $this->hint_img_url . '" class="' . $this->hint_img_class . '" title="' . $this->hint . '" alt="' . $this->hint . '" />';
			
			//add the suffix
			if($substringged == true)
			{
				$output .= $this->element_suffix;
			}
		}
		
		return $output;
	}
	
	/**
	 * Sets the element's hint message
	 *
	 * @param string the hint
	 */
	public function hint($hint)
	{
		$this->hint = $hint;
	}

	/**
	 * Opens a many group for client side many to one duplication
	 */
	public function many_open()
	{
		return $this->html;
	}

	/**
	 * Closes the many group for client side many to one duplication
	 */
	public function many_close()
	{
		return $this->html;
	}
	
	/**
	 * Extended make info to bypass the filtering of ^ marks in the rules (which are neccesary for most regular expressions)
	 * Make Info
	 * 
	 * Merge (combine or replace) element info
	 */
	function _make_info($source=NULL, $base=array(), $last_pass=FALSE) // $source = NAME or TYPE of element
	{
		if (is_array($source))
		{
			$replace = explode('|', $this->replace);
			if (count($replace) != 3) show_error(FGL_ERR.'Wrong parameter count for $replace in config file.');

			foreach ($source as $att=>$value)
			{
				switch ($att)
				{
					// classes
					case 'class':
						if ($replace[0] == 'TRUE') // replace
						{
							$base['class'] = $value;
						}
						else // combine
						{	
							$old = (array_key_exists('class', $base)) ? explode(' ', $base['class']) : array();
							$this->_trim_array($old);
	
							$new = explode(' ', $value);
							$this->_trim_array($new);
	
							$merged = array_unique(array_merge($old, $new));
							$base['class'] = implode(' ', $merged);
						}
					break;
					
					// styles
					case 'style':
						$old = array();
						$pair = array();
						$old_vals = (array_key_exists('style', $base)) ? explode(';', $base['style']) : array();
						$this->_trim_array($old_vals);
						foreach ($old_vals as $k=>$v) $pair[$k] = explode(':', $v);
						if (isset($pair)) foreach ($pair as $line) $old[trim($line[0])] = trim($line[1]);

						$new = array();
						$pair = array();
						$new_vals = explode(';', $value);
						$this->_trim_array($new_vals);
						foreach ($new_vals as $k=>$v) $pair[$k] = explode(':', $v);
						if (isset($pair)) foreach ($pair as $line) $new[trim($line[0])] = trim($line[1]);

						if ($replace[1] == 'TRUE') // replace
						{
							foreach ($new as $key=>$value)
							{
								$old[$key] = $value;
							}
							foreach ($old as $key=>$value)
							{
								$style[] = $key.':'.$value;
							}
						}
						else // preserve
						{						
							$style = array();
							$merged = array_merge($new, $old); // preserve previously set values
							foreach ($merged as $key=>$value)
							{
								if ($key && $value) $style[] = $key.':'.$value;
							}
						}
						$base['style'] = implode(';', $style);						
					break;
					
					// javascript event handlers
					case (substr($att, 0, 2) == 'on'):
						if ($replace[2] == 'TRUE') // replace
						{
							$base[$att] = $value;
						}
						else // combine
						{					
							$old = (array_key_exists($att, $base)) ? explode(';', $base[$att]) : array();
							$this->_trim_array($old);
												
							$new = explode(';', $value);
							$this->_trim_array($new);
	
							$merged = array_unique(array_merge($old, $new));
							$base[$att] = implode(';', $merged);
						}
					break;
					
					case 'rules':
						$old = (array_key_exists('rules', $base)) ? $base['rules'] : '';

						if (strstr($old, '^'))
						{
							//$new = str_replace('^', '|'.$value.'|', $old);
						}
						else
						{
							$new = $old.'|'.$value;
						}
						
						if ($last_pass)
						{
							//$new = str_replace('^', '|', $new);
						}

						$new = explode('|', $new);				
						$new = implode('|', array_unique(array_filter($new)));

						$base['rules'] = $new;
					break;
					
					default:
					// replace all other attributes by default
					$base[$att] = $source[$att];
				}
			}
		}

		return $base;
	}

}

/* End of file Superform.php */
/* Location: ./application/libraries/Superform.php */