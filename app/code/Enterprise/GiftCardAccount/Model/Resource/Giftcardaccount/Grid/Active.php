<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftCardAccount Resource Collection
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCardAccount_Model_Resource_Giftcardaccount_Grid_Active
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
            Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
            __('Yes'),
            Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
            __('No'),
        );
    }
}
