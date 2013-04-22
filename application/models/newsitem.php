<?php

/**
 * NewsItem DataMapper Model
 *
 * @author		Simon Kort
 * @link		http://www.campaigncodex.com
 */
class NewsItem extends DataMapper {
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'newsitem';
	public $table = 'newsitem';

	// You can override the database connections with this option
	// var $db_params = 'db_config_name';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that NewsItem can have just one of.
	public $has_one = array();

	// Insert related models that NewsItem can have more than one of.
	public $has_many = array();

	/* Relationship Examples
	 * For normal relationships, simply add the model name to the array:
	 *   $has_one = array('user'); // NewsItem has one User
	 *
	 * For complex relationships, such as having a Creator and Editor for
	 * NewsItem, use this form:
	 *   $has_one = array(
	 *   	'creator' => array(
	 *   		'class' => 'user',
	 *   		'other_field' => 'created_newsitem'
	 *   	)
	 *   );
	 *
	 * Don't forget to add 'created_newsitem' to User, with class set to
	 * 'newsitem', and the other_field set to 'creator'!
	 *
	 */
	 
	// Validation
	public $validation = array();

	// Default Ordering
	public $default_order_by = array('created_on' => 'desc', 'id' => 'desc');

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
	
	/**
	 * Get a specific number of newsitems. 
	 * 
	 * @param int $count Amount of newsitems to get. Defaults to 10.
	 */
	public function get_current($count=10)
	{
		return $this->limit($count)->get();
	}
	
	/**
	 * __toString()
	 */
	public function __toString()
	{
		return $this->title;
	}
}

/* End of file newsitem.php */
/* Location: ./application/models/newsitem.php */
