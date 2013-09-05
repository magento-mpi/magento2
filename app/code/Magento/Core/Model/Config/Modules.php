<?php
/**
 * Modules configuration. Contains primary configuration and configuration from modules /etc/*.xml files
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Modules implements Magento_Core_Model_ConfigInterface
{
    /**
     * Configuration data container
     *
     * @var Magento_Core_Model_ConfigInterface
     */
    protected $_data;

    /**
     * Configuration storage
     *
     * @var Magento_Core_Model_Config_StorageInterface
     */
    protected $_storage;

    /**
     * @param Magento_Core_Model_Config_StorageInterface $storage
     */
    public function __construct(Magento_Core_Model_Config_StorageInterface $storage)
    {
        \Magento\Profiler::start('config_modules_load');
        $this->_storage = $storage;
        $this->_data = $this->_storage->getConfiguration();
        \Magento\Profiler::stop('config_modules_load');
    }

    /**
     * Get configuration node
     *
     * @param string $path
     * @return \Magento\Simplexml\Element
     */
    public function getNode($path = null)
    {
        return $this->_data->getNode($path);
    }

    /**
     * Returns nodes found by xpath expression
     *
     * @param string $xpath
     * @return array
     */
    public function getXpath($xpath)
    {
        return $this->_data->getXpath($xpath);
    }

    /**
     * Create node by $path and set its value
     *
     * @param string $path separated by slashes
     * @param string $value
     * @param boolean $overwrite
     */
    public function setNode($path, $value, $overwrite = true)
    {
        $this->_data->setNode($path, $value, $overwrite);
    }

    /**
     * Reinitialize primary configuration
     */
    public function reinit()
    {
        $this->_data = $this->_storage->getConfiguration();
    }
}
