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
 * Source export entity model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Source_Export_Entity
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
     * Prepare and return array of export entities ids and their names
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array(
            'label' => __('-- Please Select --'),
            'value' => ''
        );
        foreach ($this->_exportConfig->getEntities() as $entityName => $entityConfig) {
            $options[] = array('value' => $entityName, 'label' => __($entityConfig['label']));
        }
        return $options;
    }
}
