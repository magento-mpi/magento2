<?php
/**
 * Placeholder configuration values processor. Replace placeholders in configuration with config values
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Section_Processor_Placeholder
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var array
     */
    protected $_cache = array();

    /**
     * @var array
     */
    protected $_configBaseNodes = array();

    /**
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(Mage_Core_Controller_Request_Http $request)
    {
        $this->_request = $request;
        $this->_configBaseNodes = array(
            Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE,
            Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL,
            Mage_Core_Model_Store::XML_PATH_SECURE_IN_ADMINHTML,
            Mage_Core_Model_Store::XML_PATH_SECURE_IN_FRONTEND,
            Mage_Core_Model_Store::XML_PATH_STORE_IN_URL,
            Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL,
            Mage_Core_Model_Store::XML_PATH_USE_REWRITES,
            Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL,
            Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL,
            'general/locale/code'
        );
    }

    /**
     * Replace placeholders with config values
     *
     * @param array $data
     * @return array
     */
    public function process(array $data = array())
    {
        foreach ($this->_configBaseNodes as $path) {
            $value = $this->_getValue($path, $data);
            $this->_processPlaceholders($value, '', $data);
            $this->_cache[$path] = $value;
        }
        array_walk_recursive($data, array($this, '_processPlaceholders'), $data);
        return $data;
    }

    /**
     * Replace placeholder with value
     *
     * @param string $value
     * @param string $key
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _processPlaceholders(&$value, $key, $data)
    {
        if (is_string($value) && preg_match('/{{(.*)}}.*/', $value, $matches)) {
            $placeholder = $matches[1];
            $url = false;
            if ($placeholder == 'unsecure_base_url') {
                $url = $this->_getValue(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $data);
            } elseif ($placeholder == 'secure_base_url') {
                $url = $this->_getValue(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $data);
            }

            if ($url) {
                $value = str_replace('{{' . $placeholder . '}}', $url, $value);
            } elseif (strpos($value, Mage_Core_Model_Store::BASE_URL_PLACEHOLDER) !== false) {
                $distroBaseUrl = $this->_request->getDistroBaseUrl();
                $value = str_replace(Mage_Core_Model_Store::BASE_URL_PLACEHOLDER, $distroBaseUrl, $value);
            }
        }
    }

    /**
     * Get array value by path
     *
     * @param string $path
     * @param array $data
     * @return mixed
     */
    protected function _getValue($path, array $data)
    {
        if (isset($this->_cache[$path])) {
            return $this->_cache[$path];
        }
        $keys = explode('/', $path);
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return null;
            }
        }
        $this->_cache[$path] = $data;
        return $data;
    }
}
