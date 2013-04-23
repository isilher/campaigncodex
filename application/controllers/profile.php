<?php

class Profile extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		// Load controller specific language file
		$this->load->language('cc_profile');

		// Handle messages and errors
		// TODO: Use other system
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['error'] = $this->session->flashdata('error');		
	}
	
	function index() {
		// Get account with characters
		$account = new Account();
		$account->get_by_id($this->data['user']->id);
		
		$this->data['account'] = $account;
		
		// Title, content, template
		$this->data['title'] = lang('title_index');
		$this->data['content'] = 'profile/index.php';	
		$this->load->view('template', $this->data);
	}
}

/* End of file profile.php */
/* Location: ./system/application/controllers/profile.php */