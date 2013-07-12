<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Saas
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_Maintenance_Config
{
    /**
     * @var array
     */
    protected $_config = array();

    /**
     * @param array|null $config
     */
    public function __construct($config = array())
    {
        $this->_config = is_array($config) ? $config : array();
    }

    /**
     * Get config value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getValue($key, $default = false)
    {
        $output = $default;
        if (array_key_exists($key, $this->_config)) {
            $output = $this->_config[$key];
        }
        return $output;
    }

    /**
     * Get is maintenance mode enabled
     * @return bool
     */
    public function isMaintenanceMode()
    {
        return (bool) $this->_getValue('enable');
    }

    /**
     * Get white list of IP addresses
     *
     * @return array
     */
    public function getWhiteList()
    {
        $whiteList = $this->_getValue('whitelist', '');
        $whiteList = str_replace(' ', '', $whiteList);
        $whiteList = empty($whiteList) ? array() : explode(',', $whiteList);
        return $whiteList;
    }

    /**
     * Get maintenance mode url for redirect
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->_getValue('url', null);
    }
}
