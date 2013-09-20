<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model of export file formats
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Source_Export_Format implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_ImportExport_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_ImportExport_Model_Config $config
     */
    public function __construct(Magento_ImportExport_Model_Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Prepare and return array of available export file formats.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $formats = Magento_ImportExport_Model_Export::CONFIG_KEY_FORMATS;
        return $this->_config->getModelsComboOptions($formats);
    }
}
