<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 31.08.12
 * Time: 17:52
 * To change this template use File | Settings | File Templates.
 */

class Mage_Adminhtml_Model_Wishlist_Item_Collection extends Mage_Wishlist_Model_Resource_Item_Collection
{
    public function __construct()
    {
        parent::__construct(Mage::getResourceSingleton('Mage_Wishlist_Model_Resource_Item'));
    }

    /**
     * Initialize db select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerIdFilter(Mage::registry('current_customer')->getId())
        ->resetSortOrder()
        ->addDaysInWishlist()
        ->addStoreData();
        return $this;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Varien_Data_Collection_Db
     */
    public function load($printQuery = false, $logQuery = false)
    {
        return parent::load($printQuery, $logQuery);
    }
}
