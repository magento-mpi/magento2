<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customerbalance history collection
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerBalance_Model_Resource_Balance_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CustomerBalance_Model_Balance', 'Magento_CustomerBalance_Model_Resource_Balance');
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return Magento_CustomerBalance_Model_Resource_Balance_Collection
     */
    public function addWebsitesFilter($websiteIds)
    {
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteIds);
        return $this;
    }

    /**
     * Implement after load logic for each collection item
     *
     * @return Magento_CustomerBalance_Model_Resource_Balance_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->walk('afterLoad');
        return $this;
    }
}
