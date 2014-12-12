<?php
/**
 * Import/Export Schedule entities option array
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation\Options;

class Entities implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data
     */
    protected $_modelData;

    /**
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data $model
     */
    public function __construct(\Magento\ScheduledImportExport\Model\Scheduled\Operation\Data $model)
    {
        $this->_modelData = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->_modelData->getEntitiesOptionArray();
    }
}
