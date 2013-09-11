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
namespace Magento\GiftCardAccount\Model\Resource\Giftcardaccount\Grid;

class Active
        implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Return options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_ENABLED =>
            __('Yes'),
            \Magento\GiftCardAccount\Model\Giftcardaccount::STATUS_DISABLED =>
            __('No'),
        );
    }
}
