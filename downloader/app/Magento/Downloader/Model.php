<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Model
 *
 * @category   Magento
 * @package    Magento_Connect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloader;

class Model
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
    * @return \Magento\Downloader\Controller
    */
    public function controller()
    {
        return \Magento\Downloader\Controller::singleton();
    }

    /**
    * Set value for key
    *
    * @param string $key
    * @param mixed $value
    * @return \Magento\Downloader\Model
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
