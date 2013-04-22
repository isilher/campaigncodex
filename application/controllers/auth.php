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
		// Load libraries, helpers, etc.
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->data['classes_form'] = array('class' => '');
		
		$this->form_validation->set_rules('username', $this->lang->line('create_user_validation_fname_label'), 'required');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required');		
		
		if($this->form_validation->run() == TRUE)
		{
			// Valid login form
			if($this->ion_auth->login(strtolower($this->input->post('username')), $this->input->post('password'), TRUE))
			{
				// Login successful
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('', 'refresh');
			}
			else
			{
				// Login unsuccessful
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				//redirect('auth/login', 'refresh');
			}
		}
		else
		{
			// Unvalid register form, give feedback
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			$this->data['message_type'] = 'alert-error';
		}
		
		// Title, content, template
		$this->data['title'] = lang('title_login');
		$this->data['content'] = 'auth/login.php';
		$this->load->view('template', $this->data);		
	}
	
	function logout()
	{
		//log the user out
		$logout = $this->ion_auth->logout();
		
		//redirect them to the login page
		$this->session->set_flashdata('message', $this->ion_auth->messages());
		redirect('auth/login', 'refresh');		
	}
	
	function register()
	{
		// Load libraries, helpers, etc.
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->data['classes_form'] = array('class' => 'form-horizontal');
		
		// Set form validation
		$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_fname_label'), 'required|valid_email|matches[confirmEmail]');
		$this->form_validation->set_rules('confirmEmail', $this->lang->line('create_user_validation_fname_label'), 'required');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[confirmPassword]');
		$this->form_validation->set_rules('confirmPassword', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
		$this->form_validation->set_rules('username', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
		
		if($this->form_validation->run() == TRUE)
		{
			// Valid register form, handle registering
			$username = strtolower($this->input->post('username'));
			$email = strtolower($this->input->post('email'));
			$password = $this->input->post('password');
			//TODO: Check email and username for uniqueness
			
			// Create new account
			if($this->ion_auth->register($username, $password, $email))
			{
				// Account successfully created
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('auth/login', 'refresh');
			}
			else
			{
				// Unvalid register form, give feedback
				$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			}
		}
		else
		{
			// Unvalid register form, give feedback
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
		}
		
		// Title, content, template
		$this->data['title'] = lang('title_register');
		$this->data['content'] = 'auth/register.php';
		$this->load->view('template', $this->data);		
	}
}

/* End of file auth.php */
/* Location: ./system/application/controllers/auth.php */