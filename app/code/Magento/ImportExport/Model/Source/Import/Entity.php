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
 * Source import entity model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Source_Import_Entity implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_ImportExport_Model_Import_ConfigInterface
     */
    protected $_importConfig;

    /**
     * @param Magento_ImportExport_Model_Import_ConfigInterface $importConfig
     */
    public function __construct(
        Magento_ImportExport_Model_Import_ConfigInterface $importConfig
    ) {
        $this->_importConfig = $importConfig;
    }

    /**
     * Prepare and return array of import entities ids and their names
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
        foreach ($this->_importConfig->getEntities() as $entityName => $entityConfig) {
            $options[] = array('label' => __($entityConfig['label']), 'value' => $entityName);
        }
        return $options;

    }
}
