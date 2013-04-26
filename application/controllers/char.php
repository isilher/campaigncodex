<?php

class Char extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		// Load controller specific language file
		$this->load->language('cc_char');

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
		//TODO: Debug multiple word character names
		
		// Get the character
 		$this->data['character'] = new Character();
 		$this->data['character']->where('name', $charname)->where('unique', $charunique)->get();

		// Title, content, template
		$this->data['title'] = lang('title_character');
		$this->data['content'] = 'char/index.php';	
		$this->load->view('template', $this->data);
	}
	
	function create()
	{
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->data['classes_form'] = array('class' => 'form-horizontal');
		
		$this->form_validation->set_rules('charname', lang('create_charname'), 'required');
		
		if($this->form_validation->run() == TRUE)
		{
			// Valid form
			$character = new Character();
			$character->name = $this->input->post('charname');
			$character->account_id = $this->data['user']->id;
			$character->avatar = uniqid('cc_');
			$character->unique = $this->get_uniqueid($this->input->post('charname'));
			
			$character->save();
			
			// Set feedback message
			
			$this->session->set_flashdata('message', 'Character created!');
			redirect(site_url('char/create'));
		}
		else
		{
			// Unvalid create form, give feedback
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			$this->data['message_type'] = 'alert-error';
		}
		
		// Title, content, template
		$this->data['title'] = lang('title_create');
		$this->data['content'] = 'char/create.php';
		$this->load->view('template', $this->data);		
	}
	
	private function get_uniqueid($charname)
	{
		// Check to see if we already have the uniqueid
		$character = new Character();
		$i = 0;
		while($i != 1)
		{
			// Generate a random uniqueid
			$uniqueid = rand(1000,9999);
			
			if($character->where('name', $charname)->where('unique', $uniqueid)->count() < 1)
			{
				$i = 1;
			}
		}
		
		return $uniqueid;
	}
}

/* End of file character.php */
/* Location: ./system/application/controllers/character.php */