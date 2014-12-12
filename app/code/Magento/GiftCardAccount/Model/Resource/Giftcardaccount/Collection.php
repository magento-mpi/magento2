<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Model\Resource\Giftcardaccount;

/**
 * GiftCardAccount Resource Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\GiftCardAccount\Model\Giftcardaccount',
            'Magento\GiftCardAccount\Model\Resource\Giftcardaccount'
        );
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return $this
     */
    public function addWebsiteFilter($websiteIds)
    {
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteIds);
        return $this;
    }
}
