<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for Magento_Sales_Model_Order_Payment for payment variable
 *
 * Container that can restrict access to properties and method
 * with white list. It can return info block, method code and method name.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Payment extends Saas_PrintedTemplate_Model_Variable_Abstract
{
    /**
     * Constructor
     *
     * @see Saas_PrintedTemplate_Model_Template_Variable_Abstract::__construct()
     * @param Magento_Sales_Model_Order_Payment $value
     */
    public function __construct(Magento_Sales_Model_Order_Payment $value)
    {
        parent::__construct($value);
        $this->_setListsFromConfig('payment');
    }

    /**
     * Returns method code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_value->getMethod();
    }

    /**
     * Returns HTML info block for paymen
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->_getPaymentHelper()->getInfoBlock($this->_value)->toHtml();
    }

    /**
     * Returns title of payment method
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getPaymentHelper()->getMethodInstance($this->getCode())->getTitle();
    }

    /**
     * Returns true if code of method was used is $code
     *
     * @param string $code
     * @return bool
     */
    public function isMethodUsed($code)
    {
        return $this->getCode() == $code;
    }

    /**
     * Returns payment helper
     *
     * @return Magento_Payment_Helper_Data
     */
    protected function _getPaymentHelper()
    {
        return Mage::helper('Magento_Payment_Helper_Data');
    }
}
