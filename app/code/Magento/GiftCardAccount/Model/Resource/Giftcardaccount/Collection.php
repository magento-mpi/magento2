<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftCardAccount Resource Collection
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftCardAccount_Model_Resource_Giftcardaccount_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource constructor
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Magento_GiftCardAccount_Model_Giftcardaccount',
            'Magento_GiftCardAccount_Model_Resource_Giftcardaccount'
        );
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return Magento_GiftCardAccount_Model_Resource_Giftcardaccount_Collection
     */
    public function addWebsiteFilter($websiteIds)
    {
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteIds);
        return $this;
    }
}
