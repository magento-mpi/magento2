<?php
/**
 *  Locale configuration. Contains configuration from app/locale/[locale_Code]/*.xml files
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Locales implements Magento_Core_Model_ConfigInterface
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
        $this->_storage = $storage;
        $this->_data = $this->_storage->getConfiguration();
    }

    /**
     * Get configuration node
     *
     * @param string $path
     * @return Magento_Simplexml_Element
     */
    public function getNode($path = null)
    {
        return $this->_data->getNode($path);
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
     * Reinitialize locales configuration
     */
    public function reinit()
    {
        $this->_data = $this->_storage->getConfiguration();
    }
}
