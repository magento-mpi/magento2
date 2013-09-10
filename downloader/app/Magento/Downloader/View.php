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
 * Class for viewer
 *
 * @category   Magento
 * @package    Magento_Connect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloader_View
{
    /**
     * Internal cache
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
    * Retrieve Controller as singleton
    *
    * @return Magento_Downloader_Controller
    */
    public function controller()
    {
        return Magento_Downloader_Controller::singleton();
    }

    /**
     * Create url by action and params
     *
     * @param mixed $action
     * @param mixed $params
     * @return string
     */
    public function url($action='', $params=array())
    {
        return $this->controller()->url($action, $params);
    }

    /**
     * Retrieve base url
     *
     * @return string
     */
    public function baseUrl()
    {
        return str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    }

    /**
     * Retrieve url of magento
     *
     * @return string
     */
    public function mageUrl()
    {
        return str_replace('\\', '/', dirname($this->baseUrl()));
    }

    /**
     * Include template
     *
     * @param string $name
     * @return string
     */
    public function template($name)
    {
        ob_start();
        include $this->controller()->filepath('template/'.$name);
        return ob_get_clean();
    }

    /**
    * Set value for key
    *
    * @param string $key
    * @param mixed $value
    * @return Magento_Downloader_Controller
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

    /**
     * Retrieve link for header menu
     *
     * @param mixed $action
     */
    public function getNavLinkParams($action)
    {
        $params = 'href="'.$this->url($action).'"';
        if ($this->controller()->getAction()==$action) {
            $params .= ' class="active"';
        }
        return $params;
    }
}
