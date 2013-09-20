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
     * @var Magento_ImportExport_Model_Export_ConfigInterface
     */
    protected $_exportConfig;

    /**
     * @param Magento_ImportExport_Model_Export_ConfigInterface $exportConfig
     */
    public function __construct(
        Magento_ImportExport_Model_Export_ConfigInterface $exportConfig
    ) {
        $this->_exportConfig = $exportConfig;
    }

    /**
     * Prepare and return array of import entities ids and their names
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->_exportConfig->getFileFormats() as $formatName => $formatConfig) {
            $options[] = array('value' => $formatName, 'label' => __($formatConfig['label']));
        }
        return $options;

    }
}
