<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Operation resource model
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation;

class Collection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection model
     *
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
     * @return \Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation\Collection
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $item->afterLoad();
        }

        return parent::_afterLoad();
    }
}
