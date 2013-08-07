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
     * @var Magento_GiftCardAccount_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_GiftCardAccount_Helper_Data $helper
     */
    public function __construct(Magento_GiftCardAccount_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Return options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
            $this->_helper->__('Yes'),
            Magento_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
            $this->_helper->__('No'),
        );
    }
}
