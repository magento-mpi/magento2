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
namespace Magento\ImportExport\Model\Source\Import;

class Entity implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\ImportExport\Model\Import\ConfigInterface
     */
    protected $_importConfig;

    /**
     * @param \Magento\ImportExport\Model\Import\ConfigInterface $importConfig
     */
    public function __construct(
        \Magento\ImportExport\Model\Import\ConfigInterface $importConfig
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
