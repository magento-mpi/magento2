<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ImportExport\Model\Source\Import\Behavior;

/**
 * Import behavior source model
 */
class Custom extends \Magento\ImportExport\Model\Source\Import\AbstractBehavior
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE => __('Add/Update Complex Data'),
            \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE => __('Delete Entities'),
            \Magento\ImportExport\Model\Import::BEHAVIOR_CUSTOM => __('Custom Action')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'custom';
    }
}
