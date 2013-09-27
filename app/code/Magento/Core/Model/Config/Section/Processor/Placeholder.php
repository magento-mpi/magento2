<?php
/**
 * Placeholder configuration values processor. Replace placeholders in configuration with config values
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Section_Processor_Placeholder
{
    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(Magento_Core_Controller_Request_Http_Proxy $request)
    {
        $this->_request = $request;
    }

    /**
     * Replace placeholders with config values
     *
     * @param array $data
     * @return array
     */
    public function process(array $data = array())
    {
        foreach (array_keys($data) as $key) {
            $this->_processData($data, $key);
        }
        return $data;
    }

    /**
     * Process array data recursively
     *
     * @param array $data
     * @param string $path
     */
    protected function _processData(&$data, $path)
    {
        $configValue = $this->_getValue($path, $data);
        if (is_array($configValue)) {
            foreach (array_keys($configValue) as $key) {
                $this->_processData($data, $path . '/' . $key);
            }
        } else {
            $this->_setValue($data, $path, $this->_processPlaceholders($configValue, $data));
        }
    }

    /**
     * Replace placeholders with config values
     *
     * @param string $value
     * @param array $data
     * @return string
     */
    protected function _processPlaceholders($value, $data)
    {
        $placeholder = $this->_getPlaceholder($value);
        if ($placeholder) {
            $url = false;
            if ($placeholder == 'unsecure_base_url') {
                $url = $this->_getValue(Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $data);
            } elseif ($placeholder == 'secure_base_url') {
                $url = $this->_getValue(Magento_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $data);
            }

            if ($url) {
                $value = str_replace('{{' . $placeholder . '}}', $url, $value);
            } elseif (strpos($value, Magento_Core_Model_Store::BASE_URL_PLACEHOLDER) !== false) {
                $distroBaseUrl = $this->_request->getDistroBaseUrl();
                $value = str_replace(Magento_Core_Model_Store::BASE_URL_PLACEHOLDER, $distroBaseUrl, $value);
            }

            if (null !== $this->_getPlaceholder($value)) {
                $value = $this->_processPlaceholders($value, $data);
            }
        }
        return $value;
    }

    /**
     * Get placeholder from value
     *
     * @param string $value
     * @return string|null
     */
    protected function _getPlaceholder($value)
    {
        if (is_string($value) && preg_match('/{{(.*)}}.*/', $value, $matches)) {
            $placeholder = $matches[1];
            if ($placeholder == 'unsecure_base_url'
                || $placeholder == 'secure_base_url'
                || strpos($value, Magento_Core_Model_Store::BASE_URL_PLACEHOLDER) !== false
            ) {
                return $placeholder;
            }
        }
        return null;
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
        $keys = explode('/', $path);
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return null;
            }
        }
        return $data;
    }

    /**
     * Set array value by path
     *
     * @param array $container
     * @param string $path
     * @param string $value
     */
    protected function _setValue(array &$container, $path, $value)
    {
        $segments = explode('/', $path);
        $currentPointer = &$container;
        foreach ($segments as $segment) {
            if (!isset($currentPointer[$segment])) {
                $currentPointer[$segment] = array();
            }
            $currentPointer = &$currentPointer[$segment];
        }
        $currentPointer = $value;
    }
}
