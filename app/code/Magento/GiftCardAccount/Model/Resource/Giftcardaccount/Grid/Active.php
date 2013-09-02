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
class Magento_GiftCardAccount_Model_Resource_Giftcardaccount_Grid_Active
        implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Return options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
            __('Yes'),
            Magento_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
            __('No'),
        );
    }
}
