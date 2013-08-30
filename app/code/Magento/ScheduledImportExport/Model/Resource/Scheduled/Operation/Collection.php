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
class Magento_ScheduledImportExport_Model_Resource_Scheduled_Operation_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource collection model
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Magento_ScheduledImportExport_Model_Scheduled_Operation',
            'Magento_ScheduledImportExport_Model_Resource_Scheduled_Operation'
        );
    }

    /**
     * Call afterLoad method for each item
     *
     * @return Magento_ScheduledImportExport_Model_Resource_Scheduled_Operation_Collection
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $item->afterLoad();
        }

        return parent::_afterLoad();
    }
}
