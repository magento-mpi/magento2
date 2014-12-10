<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * GiftCardAccount Resource Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftCardAccount\Model\Resource\Giftcardaccount\Grid;

class Active implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_ENABLED => __('Yes'),
            \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_DISABLED => __('No')
        ];
    }
}
