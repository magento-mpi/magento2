<?php
/**
 * DB store configuration data converter. Converts associative array to tree array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Section_Store_Converter extends Magento_Core_Model_Config_Section_Converter
{
    /**
     * @var Magento_Core_Model_Config_Section_Processor_Placeholder
     */
    protected $_processor;

    /**
     * @param Magento_Core_Model_Config_Section_Processor_Placeholder $processor
     */
    public function __construct(Magento_Core_Model_Config_Section_Processor_Placeholder $processor)
    {
        $this->_processor = $processor;
    }

    /**
     * Convert config data
     *
     * @param array $source
     * @param array $initialConfig
     * @return array
     */
    public function convert($source, $initialConfig = array())
    {
        $storeConfig = array_replace_recursive($initialConfig, parent::convert($source));
        return $this->_processor->process($storeConfig);
    }
}
