<?php

/**
 * Node DataMapper Model
 *
 * @author		Simon Kort
 * @link		http://www.campaigncodex.com
 */
class Node extends DataMapper {
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'node';
	public $table = 'node';

	// You can override the database connections with this option
	// var $db_params = 'db_config_name';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that Node can have just one of.
	public $has_one = array(
			'character' => array(
					'class' => 'character',
					'other_field' => 'nodes'),
			'parent' => array(
					'class' => 'node',
					'other_field' => 'children'),
			);

	// Insert related models that Node can have more than one of.
	public $has_many = array(
			'children' => array(
					'class' => 'node',
					'other_field' => 'parent'),
			);

	/* Relationship Examples
	 * For normal relationships, simply add the model name to the array:
	 *   $has_one = array('user'); // Node has one User
	 *
	 * For complex relationships, such as having a Creator and Editor for
	 * Node, use this form:
	 *   $has_one = array(
	 *   	'creator' => array(
	 *   		'class' => 'user',
	 *   		'other_field' => 'created_node'
	 *   	)
	 *   );
	 *
	 * Don't forget to add 'created_node' to User, with class set to
	 * 'node', and the other_field set to 'creator'!
	 *
	 */
	 
	// Validation
	public $validation = array();

	// Default Ordering
	public $default_order_by = array('type', 'value');

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

	/* Example Custom Method
	function get_open_nodes()
	{
		return $this->where('status <>', 'closed')->get();
	}
	*/

	// --------------------------------------------------------------------
	// Custom Validation Rules
	//   Add custom validation rules for this model here.
	// --------------------------------------------------------------------
	
	public function delete($object = '', $related_field = '')
	{
		foreach($this->children as $child)
		{
			$child->delete();
		}
		parent::delete($object = '', $related_field = '');
		
	}
	
	public function __toString()
	{
		return $this->type . ' ' . $this->value;
	}
}

/* End of file node.php */
/* Location: ./application/models/node.php */
