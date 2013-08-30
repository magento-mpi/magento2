<?php
/**
 * Default configuration reader
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Config_Section_Reader_DefaultReader
{
    /**
     * @var Magento_Core_Model_Config_Initial
     */
    protected $_initialConfig;

    /**
     * @var Magento_Core_Model_Config_Section_Converter
     */
    protected $_converter;

    /**
     * @var Magento_Core_Model_Resource_Config_Value_Collection_ScopedFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Magento_Core_Model_Config_Initial $initialConfig
     * @param Magento_Core_Model_Config_Section_Converter $converter
     * @param Magento_Core_Model_Resource_Config_Value_Collection_ScopedFactory $collectionFactory
     * @param Magento_Core_Model_App_State $appState
     */
    public function __construct(
        Magento_Core_Model_Config_Initial $initialConfig,
        Magento_Core_Model_Config_Section_Converter $converter,
        Magento_Core_Model_Resource_Config_Value_Collection_ScopedFactory $collectionFactory,
        Magento_Core_Model_App_State $appState
    ) {
        $this->_initialConfig = $initialConfig;
        $this->_converter = $converter;
        $this->_collectionFactory = $collectionFactory;
        $this->_appState = $appState;
    }

    /**
     * Read configuration data
     *
     * @return array
     */
    public function read()
    {
        $config = $this->_initialConfig->getDefault();
        if ($this->_appState->isInstalled()) {
            $collection = $this->_collectionFactory->create(array('scope' => 'default'));
            $dbDefaultConfig = array();
            foreach ($collection as $item) {
                $dbDefaultConfig[$item->getPath()] = $item->getValue();
            }
            $dbDefaultConfig = $this->_converter->convert($dbDefaultConfig);
            $config = array_replace_recursive($config, $dbDefaultConfig);
        }
        return $config;
    }
}
