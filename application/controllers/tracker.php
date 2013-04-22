<?php

class Tracker extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		// Run debugger if we are running on the localhost
		if($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
			//$this->output->enable_profiler(TRUE);
		}
	}
	
	function index() {
		$data['test'] = 'test';
		$this->load->view('tracker/tracker', $data);
	}
}

/* End of file tracker.php */
/* Location: ./system/application/controllers/tracker.php */