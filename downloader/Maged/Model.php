<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
* Class Model
*
* @category   Mage
* @package    Mage_Connect
* @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Maged_Model
{

    /**
    * Internal cache
    *
    * @var array
    */
    protected $_data;

    /**
    * Constructor
    */
    public function __construct()
    {
        $args = func_get_args();
        if (empty($args[0])) {
            $args[0] = array();
        }
        $this->_data = $args[0];

        $this->_construct();
    }

    /**
    * Constructor for covering
    */
    protected function _construct()
    {

    }

    /**
    * Retrieve controller
    * @return Maged_Controller
    */
    public function controller()
    {
        return Maged_Controller::singleton();
    }

    /**
    * Set value for key
    *
    * @param string $key
    * @param mixed $value
    * @return Maged_Model
    */
    public function set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
    * Get value by key
    *
    * @param string $key
    * @return mixed
    */
    public function get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }
}
