<?php

class Char extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		// Load controller specific language file
		$this->load->language('cc_character');

		// Handle messages and errors
		// TODO: Use other system
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['error'] = $this->session->flashdata('error');		
	}
	
	function index($charstring=0) {
		// Split charname and unique ID
		$charstring = explode('-', $charstring);
		$charname = $charstring[0];
		$charunique = $charstring[1];
		
		//echo $charname . ' ' . $charunique;
		
		// Get the character
 		$this->data['character'] = new Character();
 		$this->data['character']->where('name', $charname)->where('unique', $charunique)->get();

		// Title, content, template
		$this->data['title'] = lang('character');
		$this->data['content'] = 'character/index.php';	
		$this->load->view('template', $this->data);
	}
	
	function test()
	{
		$character = new Character();
		$character->get();
	}
}

/* End of file character.php */
/* Location: ./system/application/controllers/character.php */