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
class Magento_GiftCardAccount_Model_Resource_Giftcardaccount_Grid_Status
    implements Magento_Core_Model_Option_ArrayInterface
{

    /**
     * @var Magento_GiftCardAccount_Model_Giftcardaccount
     */
    protected $_model;

    /**
     * @param Magento_GiftCardAccount_Model_Giftcardaccount $model
     */
    public function __construct(Magento_GiftCardAccount_Model_Giftcardaccount $model)
    {
        $this->_model = $model;
    }

    /**
     * Return states options list
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_model->getStatesAsOptionList();
    }
}
