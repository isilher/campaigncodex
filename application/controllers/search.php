<?php

class Search extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		// Load controller specific language file
		$this->load->language('cc_search');

		// Handle messages and errors
		// TODO: Use other system
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['error'] = $this->session->flashdata('error');		
	}
	
	function index() {
		// Load extra libraries, helpers, etc.
		$this->load->helper('charlink');
		
		// Handle search
		$post = $this->input->post();
		//TODO: For now POST, do with GET in future
		//TODO: Expand search with more then just characters
		$this->data['characters'] = new Character();
		$this->data['characters']->ilike('name', $post['search'])->get();
		
		// Title, content, template
		$this->data['title'] = lang('search');
		$this->data['content'] = 'search/index.php';	
		$this->load->view('template', $this->data);
	}
}

/* End of file search.php */
/* Location: ./system/application/controllers/search.php */