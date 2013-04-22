<?php

class Auth extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		// Load controller specific language file
		$this->load->language('cc_auth');

		// Handle messages and errors
		// TODO: Use other system
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['error'] = $this->session->flashdata('error');		
	}
	
	function index() {

		// Title, content, template
		//$this->data['title'] = lang('auth');
		$this->data['content'] = 'auth/index.php';	
		$this->load->view('template', $this->data);
	}
	
	function login()
	{
		
	}
	
	function register()
	{
		// Load libraries, helpers, etc.
		$this->load->helper('form');
		$this->data['classes_form'] = array('class' => 'form-horizontal');
		
		
		// Title, content, template
		$this->data['title'] = lang('title_register');
		$this->data['content'] = 'auth/register.php';
		$this->load->view('template', $this->data);		
	}
}

/* End of file auth.php */
/* Location: ./system/application/controllers/auth.php */