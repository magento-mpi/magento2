<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reader for cron parameters from data base storage
 */
class Magento_Cron_Model_Config_Reader_Db
{
    /**
     * Converter instance
     *
     * @var Magento_Cron_Model_Config_Converter_Db
     */
    protected $_converter;

    /**
     * @var Magento_Core_Model_Config_Section_Reader_DefaultReader
     */
    protected $_defaultReader;

    /**
     * Initialize parameters
     *
     * @param Magento_Core_Model_Config_Section_Reader_DefaultReader $defaultReader
     * @param Magento_Cron_Model_Config_Converter_Db                 $converter
     */
    public function __construct(
        Magento_Core_Model_Config_Section_Reader_DefaultReader $defaultReader,
        Magento_Cron_Model_Config_Converter_Db $converter
    ) {
        $this->_defaultReader = $defaultReader;
        $this->_converter = $converter;
    }

    /**
     * Return converted data
     *
     * @return array
     */
    public function get()
    {
        return $this->_converter->convert($this->_defaultReader->read());
    }
}
