<?php

class Test extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['error'] = $this->session->flashdata('error');		
	}
	
	function index() {
		$this->data['title'] = 'Test index';
		$this->data['content'] = 'test/index.php';	
		$this->load->view('template', $this->data);
	}
	
	function node_display() {
		$character = new Character();
		$character->get_by_id(1);
		
		$this->data['character'] = $character;
		
		$this->data['title'] = 'Node display';
		$this->data['content'] = 'test/node_display.php';
		$this->load->view('template', $this->data);		
	}
	
	function node_char($id=0) {
		if($id==0)
		{
			$id = 1;
		}
		// Load form helper
		$this->load->helper('form');
		
		// Load form validation
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span class="help-inline">', '</span>');
		
		$character = new Character();
		$character->get_by_id($id);
		
		// If the character doesn't exsists yet, create it
		if(!$character->exists())
		{
			// Make and save the character
			$character->clear();
			$character->id = $id;
			$character->name = rand(10000, 99999);
			$character->save();
			
			// Reload the character
			$character->get_by_id($id);
		}
	
		// Set validation rules
		$this->form_validation->set_rules('type', 'Type', 'required');
		
		// Run validation
		if($this->form_validation->run())
		{
			$post = $this->input->post();
			// Valid form, create new node
			$node = new Node();
			$node->character_id = $id;
			$node->type = $post['type'];
			$node->value = $post['value'];
			
			if($node->type !== 'prime')
			{
				$parent = new Node();
				$parent->where('id', $post['parent'])->get();

				// Save the node
				$node->save_parent($parent);
					
				$this->session->set_flashdata('message', 'Node created');
			}
			else
			{
				// This is the prime node, so only save the node
				$node->save();
				
				$this->session->set_flashdata('message', 'Prime node created');
			}
			redirect('test/node_char/' . $id);
		}
		
		// Create the type options
		$this->data['type_options'] = array(
					'alignment'			=> 'Alignment',
					'age-base'			=> 'Age',
					'stat-base-str' 	=> 'Base stat: Strength',
					'stat-base-dex' 	=> 'Base stat: Dexterity',
					'stat-base-con' 	=> 'Base stat: Constitution',
					'stat-base-int' 	=> 'Base stat: Intelligence',
					'stat-base-wis' 	=> 'Base stat: Wisdom',
					'stat-base-cha' 	=> 'Base stat: Charisma',
					'class'				=> 'Class',				
					'favoredclass'		=> 'Favored class',
					'favoredclass-bonus'=> 'Favored class bonus',
					'feat'				=> 'Feat',
					'height-base'		=> 'Height',
					'hp'				=> 'Hitpoints',
					'level'				=> 'Level',
					'race' 				=> 'Race',
					'racialtrait'		=> 'Racialtrait',
					'weight-base'		=> 'Weight',
					'prime'				=> 'Prime (create only one!)'
				);
		
		// Get all the parent nodes possible and put them in an array
		$this->data['parent_options'] = array();
		
		$feats = new Rule();
		$this->data['feat_options'] = $feats->get_feat_options();
		
		foreach($character->nodes as $parent)
		{
			$this->data['parent_options'][$parent->id] = $parent->type . ' [' . $parent->value . ']';
		}
		
		$this->data['prime'] = $character->nodes->where('type', 'prime')->where('character_id', $id)->get();
		$this->data['classes_form'] = array('class' => 'form-horizontal');
		$this->data['character'] = $character;
		
		$this->data['title'] = 'Node creation';
		$this->data['content'] = 'test/node_char.php';
		$this->load->view('template', $this->data);
	}
	
	public function node_delete($id=0)
	{
		if($id===0)
		{
			redirect(base_url());
		}
		
		$node = new Node();
		$node->get_by_id($id);
		
		$node->delete();
		
		$this->session->set_flashdata('message', 'Node deleted');
		
		redirect('test/node_char');
	}
}

/* End of file tracker.php */
/* Location: ./system/application/controllers/tracker.php */