<?php

class Home extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		// Load controller specific language file
		$this->load->language('cc_home');

		// Handle messages and errors
		// TODO: Use other system
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['error'] = $this->session->flashdata('error');		
	}
	
	function index() {
		// Load extra assets
		$this->load->helper('datetime');
		
		// Get the news
		$this->data['news'] = new NewsItem();
		$this->data['news']->get_current();
		
		// Title, content, template
		$this->data['title'] = lang('home');
		$this->data['content'] = 'home/index.php';	
		$this->load->view('template', $this->data);
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */