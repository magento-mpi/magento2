<?php
/**
 * Configuration data metadata processor
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_MetadataProcessor
{
    /**
     * @var Mage_Core_Model_Config_Data_BackendModelPool
     */
    protected $_backendModelPool;

    /**
     * @var array
     */
    protected $_metadata = array();

    /**
     * @param Mage_Core_Model_Config_Data_BackendModelPool $backendModelPool
     * @param Mage_Core_Model_Config_Initial $initialConfig
     */
    public function __construct(
        Mage_Core_Model_Config_Data_BackendModelPool $backendModelPool,
        Mage_Core_Model_Config_Initial $initialConfig
    ) {
        $this->_backendModelPool = $backendModelPool;
        $this->_metadata = $initialConfig->getMetadata();
    }

    /**
     * Retrieve array value by path
     *
     * @param array $data
     * @param string $path
     * @return string|null
     */
    protected function _getValue(array $data, $path)
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

    /**
     * Process config data
     *
     * @param array $data
     * @return array
     */
    public function process(array $data)
    {
        foreach ($this->_metadata as $path => $metadata) {
            /** @var Mage_Core_Model_Config_Data_BackendModelInterface $backendModel */
            $backendModel = $this->_backendModelPool->get($metadata['backendModel']);
            $value = $backendModel->processValue($this->_getValue($data, $path));
            $this->_setValue($data, $path, $value);
        }
        return $data;
    }
}
