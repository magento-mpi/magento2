<?php
/**
 * Default configuration reader
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_Config_Section_Reader_DefaultReader
{
    /**
     * @var Mage_Core_Model_Config_Initial
     */
    protected $_initialConfig;

    /**
     * @var Mage_Core_Model_Config_Section_Converter
     */
    protected $_converter;

    /**
     * @var Mage_Core_Model_Resource_Config_Value_Collection_ScopedFactory
     */
    protected $_collectionFactory;

    /**
     * @param Mage_Core_Model_Config_Initial $initialConfig
     * @param Mage_Core_Model_Config_Section_Converter $converter
     * @param Mage_Core_Model_Resource_Config_Value_Collection_ScopedFactory $collectionFactory
     */
    public function __construct(
        Mage_Core_Model_Config_Initial $initialConfig,
        Mage_Core_Model_Config_Section_Converter $converter,
        Mage_Core_Model_Resource_Config_Value_Collection_ScopedFactory $collectionFactory
    ) {
        $this->_initialConfig = $initialConfig;
        $this->_converter = $converter;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Read configuration data
     *
     * @return array
     */
    public function read()
    {
        $collection = $this->_collectionFactory->create(array('scope' => 'default'));
        $dbDefaultConfig = array();
        foreach ($collection as $item) {
            $dbDefaultConfig[$item->getPath()] = $item->getValue();
        }
        $dbDefaultConfig = $this->_converter->convert($dbDefaultConfig);
        return array_replace_recursive($this->_initialConfig->getDefault(), $dbDefaultConfig);
    }
}
