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
     * @var Enterprise_GiftCardAccount_Helper_Data
     */
    protected $_helper;

    /**
     * @param Enterprise_GiftCardAccount_Helper_Data $helper
     */
    public function __construct(Enterprise_GiftCardAccount_Helper_Data $helper)
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
            Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
            $this->_helper->__('Yes'),
            Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
            $this->_helper->__('No'),
        );
    }
}
