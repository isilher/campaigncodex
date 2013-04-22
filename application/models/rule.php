<?php

/**
 * Rule DataMapper Model
 *
 * @author		Simon Kort
 * @link		http://www.campaigncodex.com
 */
class Rule extends DataMapper {
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'node';
	public $table = 'rule';

	// You can override the database connections with this option
	// var $db_params = 'db_config_name';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that Node can have just one of.
	public $has_one = array();

	// Insert related models that Node can have more than one of.
	public $has_many = array();

	 
	// Validation
	public $validation = array();

	// Default Ordering
	public $default_order_by = array('name');

	/**
	 * Constructor: calls parent constructor
	 */
    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

	// --------------------------------------------------------------------
	// Post Model Initialisation
	//   Add your own custom initialisation code to the Model
	// The parameter indicates if the current config was loaded from cache or not
	// --------------------------------------------------------------------
	public function post_model_init($from_cache = FALSE)
	{
	}

	// --------------------------------------------------------------------
	// Custom Methods
	//   Add your own custom methods here to enhance the model.
	// --------------------------------------------------------------------

	
	function get_feat_options()
	{
		// Get all feats
		$this->where('type', 'feat')->get();
		
		// Format the feats into a options array
		$feat_options = array();
		foreach($this as $feat)
		{
			$feat_options[$feat->name] = $feat->name;
		}
		
		return $feat_options;
	}
	

	// --------------------------------------------------------------------
	// Custom Validation Rules
	//   Add custom validation rules for this model here.
	// --------------------------------------------------------------------

	
	public function __toString()
	{
		return $this->name;
	}
}

/* End of file node.php */
/* Location: ./application/models/node.php */
