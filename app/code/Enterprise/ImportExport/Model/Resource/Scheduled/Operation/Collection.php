<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Operation resource model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Model_Resource_Scheduled_Operation_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource collection model
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Enterprise_ImportExport_Model_Scheduled_Operation',
            'Enterprise_ImportExport_Model_Resource_Scheduled_Operation'
        );
    }

    /**
     * Call afterLoad method for each item
     *
     * @return Enterprise_ImportExport_Model_Resource_Scheduled_Operation_Collection
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $item->afterLoad();
        }

        return parent::_afterLoad();
    }
}
