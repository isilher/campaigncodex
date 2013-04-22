<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Load jQuery
|--------------------------------------------------------------------------
|
| If set to TRUE, load the jQuery library if it was not allready loaded
|
| If set to FALSE, jQuery is not loaded by the CIP library, you'll have
|  to load the library somewhere else
|
*/
$config['cip_load_jquery'] = TRUE;

/*
|--------------------------------------------------------------------------
| Use Javascript to beautify/animate the messages
|--------------------------------------------------------------------------
|
| If set to TRUE, use javascript to display the messages
|
| If set to FALSE, only output HTML with the messages included
|  animations and AJAX functionality will then be disabled
|
*/
$config['cip_use_javascript'] = TRUE;

/*
|--------------------------------------------------------------------------
| Use Javascript to animate the messages
|--------------------------------------------------------------------------
|
| If set to TRUE, use javascript to animate the messages
|  'cip_use_javascript' must be set to TRUE for this to work
|
| If set to FALSE, message will not be animated
|
*/
$config['cip_use_animation'] = TRUE;

/*
|--------------------------------------------------------------------------
| Export templates
|--------------------------------------------------------------------------
|
| If set to TRUE, export the templates and defaults to a <script> block
|  in the HTML. This way when you call cip_write() in from the cip.js file
|  the defaults and templates will be honoured.
|
| If set to FALSE, the defaults and templates will be merged on the server
|  side, resulting in cleaner HTML. When calling cip_write from javascript
|  on the client side you do NOT get the correct defaults and templates.
|
*/
$config['cip_export_templates'] = TRUE;

/*
|--------------------------------------------------------------------------
| Noty defaults
|--------------------------------------------------------------------------
|
| Default values as used by Noty
|
*/
$config['cip_noty_defaults'] = array(
	'layout' => 'inline', // bottom/bottomCenter/bottomLeft/bottomRight/center/centerLeft/centerRight/inline/top/topCenter/topLeft/topRight
	'theme' => 'cip_theme', // default
	'type' => 'message' // error/confirmation
);

/*
|--------------------------------------------------------------------------
| Noty type templates
|--------------------------------------------------------------------------
|
| Default values for every type as used by Noty
|
*/
$config['cip_noty_templates'] = array(
	'message'     => array(),
	'error'       => array(
		'layout' => 'center',
		'closeWith' => array('button'),
		'buttons' => array(
			array(
				'addClass' => 'btn btn-primary',
				'text' => 'ok',
				'onClick' => 'function($noty){$noty.close();}'
			)
		)		
	),
	'confirm'     => array(
		'layout' => 'center',
		'buttons' => array(
			array(
				'addClass' => 'btn btn-primary',
				'text' => 'ok',
				'onClick' => 'function($noty){$noty.close();}'
			),
			array(
				'addClass' => 'btn btn-primary',
				'text' => 'cancel',
				'onClick' => 'function($noty){$noty.close();}'
			)
		)				
	),
	'alert'       => array(
		'layout' => 'center',
		'buttons' => array(
			array(
				'addClass' => 'btn btn-primary',
				'text' => 'ok',
				'onClick' => 'function($noty){$noty.close();}'
			)
		)		
	),
	'success'     => array(),
	'warning'     => array(),
	'information' => array()
);


/* End of file cip_config.php */
/* Location: ./application/config/cip_config.php */
