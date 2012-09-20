<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 31.08.12
 * Time: 17:52
 * To change this template use File | Settings | File Templates.
 */

class Enterprise_Wishlist_Model_Item_Collection extends Enterprise_Wishlist_Model_Resource_Item_Collection
{
    public function __construct()
    {
        parent::__construct(Mage::getResourceSingleton('Enterprise_Wishlist_Model_Resource_Item'));
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
}
