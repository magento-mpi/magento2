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
namespace Magento\ImportExport\Model\Source\Export;

class Entity implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\ImportExport\Model\Export\ConfigInterface
     */
    protected $_exportConfig;

    /**
     * @param \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig
     */
    public function __construct(
        \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig
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
