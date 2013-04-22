<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * cip_lib Class
 *
 * @package		Code Ignited Proclamations
 * @version		1.0
 * @author 		Hendrik Jan van Meerveld <h.j.vanmeerveld@uu.nl>
 * @author 		Rik van Duijn <r.vanduijn@uu.nl>
 */
class Cip {

	/**
	 * CodeIgniter instance
	 *
     */
	private $CI;
	
	/** 
	 * Message store
	 *
	 */
	private $messages = array();
	
	/**
	 * Variable name, where to save messages in flash_memory
	 */
	private $_session_var 		= 'flash_message';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// set up CI classes
		$this->CI =& get_instance();
		
		// load URL helper
		$this->CI->load->helper('url');
		
		// load session library
		$this->CI->load->library('session');
		
		// load CIP config
		$this->CI->config->load('cip');		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set message
	 *
	 * @param string $message
	 * @param string $type
	 * @param array  $options
	 * @return void
	 */
    function cip_set($message, $type = '', $options = array())
	{	
		// set message
		$new_message = array_merge(
			array(
				'type' 		 => $type,
				'text'	 	 => $message				
			),
			$options
		);
		
		// create a unique key to identify the message and prevent duplicates
		$unique_key = md5(json_encode($new_message));

		// add to $this->messages
		$this->messages[$unique_key] = $new_message;
		
		// also add to session
		$session_messages = $this->CI->session->userdata($this->_session_var);
		$session_messages[$unique_key] = $new_message;
		$this->CI->session->set_userdata($this->_session_var, $session_messages);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Output html messages
	 *
	 * @param  string $label show all messages from flash_memory with this label set
	 * @return string html
	 */
    function cip_get($label = '')
	{
		// get messages defined in the current page
		$current_messages = $this->messages;
		
		// get messages saved in the session
		$session_messages = $this->CI->session->userdata($this->_session_var);
		
		// combine current and flash messages
		$messages = array();
		
		if ($current_messages AND is_array($current_messages))
		{
			$messages = array_merge($messages, $current_messages);
		}
		if ($session_messages AND is_array($session_messages))
		{
			$messages = array_merge($messages, $session_messages);
		}
		
		// set default output
		$output = '';
		
		// remember messages that we put in the HTML to also put in JS
		$cip_messages = array();
		
		// get the defaults for noty
		$defaults = $this->CI->config->item('cip_noty_defaults');
		// also load the templates
		$templates = $this->CI->config->item('cip_noty_templates');

		if ($this->CI->config->item('cip_use_javascript')) {
			$output .= "<noscript>\n";
		}

		// create the same HTML as Noty would, that way you only need to
		// modify the noty css and the HTML css will automatically be the same
		// TODO what do we do when layout is not "top"?
		$output .= '<ul class="cip cip_nojs cip_'.$defaults['layout'].'">'."\n";

		// loop messages
		$index = 0;
		foreach ($messages as $unique_key => $message) 
		{
			// get the label of the current message
			$mlabel = isset($message['label']) ? $message['label'] : '';
			
			if ($mlabel !== '' && $mlabel !== $label)
			{
				// this message is not meant to be displayed here
				// leave the message in the session userdata and do nothing with it
				continue;
			}
			
			// this message is to be displayed on the current page
			// remove it from the session userdata so it will not be displayed again later
			$cip_messages[] = $messages[$unique_key];
			unset($messages[$unique_key]);
			$this->CI->session->set_userdata($this->_session_var, $messages);
			
			$type = isset($message['type']) ? $message['type'] : $defaults['type'];
			
			// see if there is a template in the configuration for the current type
			if (isset($templates[$type]))
			{
				$template = $templates[$type];
			} 
			else
			{
				$template = array();
			}

			// merge message with defaults and template
			$message = array_merge(
				$defaults,
				$template,
				$message
			);
			
			// merge the default options with the message options
			$noty_options = array_merge($defaults, $message);

			$class_names = array('cip_'.$noty_options['type']);
			if ($index === 0) {    
				$class_names[] = 'cip_first';
			}
			if ($index++ === count($messages)-1) {
				$class_names[] = 'cip_last';
			}
			
			// add message to output
			$output .= '<li class="'.implode(' ',$class_names).'">'."\n";
			$output .= '<div class="noty_bar">'."\n";
			$output .= '<div class="noty_message">'."\n";
			$output .= '<span class="noty_text">'."\n";
			$output .= $message['text'];
			$output .= "</span>\n";	
			$output .= "</div>\n";	
			$output .= "</div>\n";	
			$output .= "</li>\n";	

		}

		$output .= "\n</ul>";

		// if animation is disabled, change the noty defaults accordingly
		if (! $this->CI->config->item('cip_use_animation')) {
			$defaults['animation'] = array(
				'open' => array('height' => 'toggle'),
				'close' => array('height' => 'toggle'),
				'easing' => 'swing',
				'speed' => 0
			);				
		}

		if ($this->CI->config->item('cip_use_javascript')) {
			$output .= "</noscript>";
			$output .= "\n<script>";
			$output .= "\nvar cip_messages = " . $this->_js_string_to_function(json_encode($cip_messages));
			if ($this->CI->config->item('cip_export_templates')) {
				// export templates and defaults
				$output .= ";\nvar cip_defaults = " . $this->_js_string_to_function(json_encode($defaults));
				$output .= ";\nvar cip_templates = " . $this->_js_string_to_function(json_encode($templates));
			}
			$output .= ";\n</script>\n";
			$output .= "\n<div id=\"cip\"></div>";
		}

		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Take the input string and replace string representations of javascript
	 * functions with the javascript functions (usefull for callback functions)
	 * 
	 * String representations of functions start with
	 * "'function("
	 * and end with
	 * "}'"
	 */
	private function _js_string_to_function($input_string)
	{
		// change a string to a function
		// use a callback to also change escaped double quotes to unescaped double quotes
		$output_string = preg_replace_callback(
			'/\"(function[(].*})\"/U',
			function($matches) { return str_replace('\"','"',$matches[1]); },
			$input_string
		);
		return $output_string;
	}
	
}
// END Cip_lib Class

/* End of file cip_lib.php */
/* Location: ./system/libraries/cip_lib.php */