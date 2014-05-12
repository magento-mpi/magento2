<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Source\Export;

/**
 * Source export entity model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Entity implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\ImportExport\Model\Export\ConfigInterface
     */
    protected $_exportConfig;

    /**
     * @param \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig
     */
    public function __construct(\Magento\ImportExport\Model\Export\ConfigInterface $exportConfig)
    {
        $this->_exportConfig = $exportConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array('label' => __('-- Please Select --'), 'value' => '');
        foreach ($this->_exportConfig->getEntities() as $entityName => $entityConfig) {
            $options[] = array('value' => $entityName, 'label' => __($entityConfig['label']));
        }
        return $options;
    }
}
