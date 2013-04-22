<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * PHP LDAP CLASS FOR MANIPULATING NAM
 *
 * PHP Version 5 with SSL and LDAP support
 *
 * @author based on adLDAP by Scott Barnett, Richard Hyland
 * @author expanded and modified for NAM by Simon Kort
 * @version 1.0.0
 */

/**
 * Main namLDAP class
 *
 * Can be initialised using $namldap = new namLDAP();
 */
class Namldap {
	/**
	 * The account suffix for your domain, can be set when the class is invoked
	 *
	 * @var string
	 */
	protected $_account_suffix = '@soliscom.uu.nl';
	
	/**
	 * The base dn for your domain
	 *
	 * @var string
	 */
	protected $_base_dn = 'ou=Identities,o=UU';
	
	/**
	 * Array of domain controllers. Specifiy multiple controllers if you
	 * would like the class to balance the LDAP queries amongst multiple servers
	 *
	 * @var array
	 */
	protected $_domain_controllers = array ('ldapx.uu.nl');
	
	/**
	 * Optional account with higher privileges for searching
	 * This should be set to a domain admin account
	 *
	 * @var string
	 * @var string
	 */
	protected $_nam_username = '';
	protected $_nam_password = '';
	
	/**
	 * Use SSL (LDAPS)
	 *
	 * @var bool
	 */
	protected $_use_ssl = TRUE;

	/**
	 * Connection and bind default variables
	 *
	 * @var mixed
	 * @var mixed
	 */
	protected $_conn;
	protected $_bind;

	/**
	 * Constructor
	 *
	 * @param array $options Array of options to pass to the constructor
	 * @throws Exception - if unable to bind to Domain Controller
	 * @return bool
	 */
	function __construct($options=array()){
		// You can specifically overide any of the default configuration options setup above
		if (count($options) > 0){
			if (array_key_exists("base_dn",$options)){
				$this->_base_dn=$options["base_dn"];
			}
			if (array_key_exists("domain_controllers",$options)){
				$this->_domain_controllers=$options["domain_controllers"];
			}
			if (array_key_exists("nam_username",$options)){
				$this->_nam_username=$options["nam_username"];
			}
			if (array_key_exists("nam_password",$options)){
				$this->_nam_password=$options["nam_password"];
			}
			if (array_key_exists("use_ssl",$options)){
				$this->_use_ssl=$options["use_ssl"];
			}
		}
	
		if ($this->ldap_supported() === FALSE) {
			throw new adLDAPException('No LDAP support for PHP.  See: http://www.php.net/ldap');
		}
		
		return $this->connect();
	}
	
	/**
	 * Destructor
	 *
	 * @return void
	 */
	function __destruct(){
		// close the connection
		$this->close();
	}	
	
	/**
	 * Connects and Binds to the Domain Controller
	 *
	 * @return bool
	 */
	public function connect() {
		// Connect to the NAM/LDAP server as the username/password
		$dc = $this->random_controller();
		
		if ($this->_use_ssl)
		{
			$this->_conn = ldap_connect("ldaps://".$dc, 636);
		} 
		else 
		{
			$this->_conn = ldap_connect($dc);
		}
		 
 		// Set some ldap options for talking to AD
  		ldap_set_option($this->_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
 		ldap_set_option($this->_conn, LDAP_OPT_REFERRALS, 0);
		 
		// Bind with higher permissions if set
 		if ($this->_nam_username != NULL && $this->_nam_password != NULL)
 		{
 			$this->_bind = ldap_bind($this->_conn, $this->_nam_username , $this->_nam_password);

 			if (!$this->_bind)
 			{
 				if ($this->_use_ssl)
 				{
 					// If you have problems troubleshooting, remove the @ character from the ldap_bind command above to get the actual error message
 					throw new namLDAPException('Bind to Active Directory failed. Either the LDAPs connection failed or the login credentials are incorrect. NAM said: ' . $this->get_last_error());
 				} 
 				else 
 				{
 					throw new namLDAPException('Bind to Active Directory failed. Check the login credentials and/or server details. NAM said: ' . $this->get_last_error());
 				}
 			}
 		}
 		
 		return (TRUE);
	}

	/**
	 * Closes the LDAP connection
	 *
	 * @return void
	 */
	public function close() 
	{
		ldap_close($this->_conn);
	}	
	
	/**
	 * Validate a user's login credentials
	 *
	 * @param string $username A user's NAM username
	 * @param string $password A user's NAM password
	 * @param bool optional $prevent_rebind
	 * @return bool
	 */
	public function authenticate($username, $password, $prevent_rebind = FALSE)
	{
        // Prevent null binding
        if ($username===NULL || $password===NULL){ return (FALSE); } 
        if (empty($username) || empty($password)){ return (FALSE); }
        
        // Bind as the user
        $this->_bind = @ldap_bind($this->_conn, 'cn=' . $username . ',ou=Identities,o=UU' , $password);
        
        
        if (!$this->_bind)
        { 
        	// we did not authenticate correctly
        	return (FALSE);
        }
        
	    // Once we've checked their details, kick back into admin mode if we have it
        if ($this->_nam_username != NULL AND !$prevent_rebind){
            $this->_bind = @ldap_bind($this->_conn , $this->_nam_username, $this->_nam_password);
            
            if (!$this->_bind)
            {
                // This should never happen in theory
                throw new namLDAPException('Rebind to NAM failed. NAM said: ' . $this->get_last_error());
            } 
        }   
        
        return (TRUE);
	}
		
	
	/**
	 * Find information about the users
	 *
	 * @param string $username The username to query
	 * @param array $fields Array of parameters to query
	 * 
	 * @return array
	 */
	public function user_info($username, $fields = NULL)
	{
		if ($username === NULL)
		{
			return (FALSE);
		}
		
		if (!$this->_bind)
		{
			return (FALSE);
		}
	
		// set the filter
		$filter = "uuShortID=" . $username;
		
		if ($fields === NULL)
		{
			//TODO: Andere standaard velden
			$fields=array('*');
		}
		
		// search NAM
		$sr = ldap_search($this->_conn, $this->_base_dn, $filter, $fields);
		
		// grab found entries
		$entries = ldap_get_entries($this->_conn, $sr);
		
		return ($entries);
	}
		
	/**
	 * Get last error from NAM
	 *
	 * This function gets the last message from NAM
	 * This may indeed be a 'Success' message but if you get an unknown error
	 * it might be worth calling this function to see what errors were raised
	 *
	 * return string
	 */
	public function get_last_error() 
	{
		return @ldap_error($this->_conn);
	}	
	
	/**
	 * Detect LDAP support in php
	 *
	 * @return bool
	 */
	protected function ldap_supported() 
	{
		if (!function_exists('ldap_connect')) 
		{
			return (FALSE);
		}
		
		return (TRUE);
	}

	/**
	 * Select a random domain controller from your domain controller array
	 *
	 * @return string
	 */
	protected function random_controller()
	{
		mt_srand(doubleval(microtime()) * 100000000); // For older PHP versions
		return ($this->_domain_controllers[array_rand($this->_domain_controllers)]);
	}
}

/**
 * namLDAP Exception Handler
 *
 * Exceptions of this type are thrown on bind failure or when SSL is required but not configured
 * Example:
 * try {
 *   $adldap = new adLDAP();
 * }
 * catch (adLDAPException $e) {
 *   echo $e;
 *   exit();
 * }
 */
class namLDAPException extends Exception {}

?>