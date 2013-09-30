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
 * Multishipping checkout payment information data
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Multishipping_Payment_Info extends Magento_Payment_Block_Info_ContainerAbstract
{
    /**
     * @var Magento_Checkout_Model_Type_Multishipping
     */
    protected $_multishipping;

    /**
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Type_Multishipping $multishipping
     * @param array $data
     */
    public function __construct(
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Type_Multishipping $multishipping,
        array $data = array()
    ) {
        $this->_multishipping = $multishipping;
        parent::__construct($paymentData, $coreData, $context, $data);
    }

    /**
     * Retrieve payment info model
     *
     * @return Magento_Payment_Model_Info
     */
    public function getPaymentInfo()
    {
        return $this->_multishipping->getQuote()->getPayment();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        $block = $this->getChildBlock($this->_getInfoBlockName());
        if ($block) {
            $html = $block->toHtml();
        }
        return $html;
    }
}
