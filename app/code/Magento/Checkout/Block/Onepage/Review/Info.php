<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout order review
 */
class Magento_Checkout_Block_Onepage_Review_Info extends Magento_Sales_Block_Items_Abstract
{
    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Session $checkoutSession,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->_checkoutSession->getQuote()->getAllVisibleItems();
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        return $this->_checkoutSession->getQuote()->getTotals();
    }
}
