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
namespace Magento\GiftCardAccount\Model\Resource\Giftcardaccount;

class Collection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource constructor
     *
     */
    protected function _construct()
    {
        $this->_init(
            '\Magento\GiftCardAccount\Model\Giftcardaccount',
            '\Magento\GiftCardAccount\Model\Resource\Giftcardaccount'
        );
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return \Magento\GiftCardAccount\Model\Resource\Giftcardaccount\Collection
     */
    public function addWebsiteFilter($websiteIds)
    {
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteIds);
        return $this;
    }
}
