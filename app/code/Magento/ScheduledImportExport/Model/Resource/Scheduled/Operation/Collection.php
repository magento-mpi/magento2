<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation;

/**
 * Operation resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
            'Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation'
        );
    }

    /**
     * Call afterLoad method for each item
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $item->afterLoad();
        }

        return parent::_afterLoad();
    }
}
