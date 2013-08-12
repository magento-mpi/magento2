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
 * Container for Magento_Sales_Model_Order_Creditmemo for creditmemo variable
 *
 * Container that can restrict access to properties and method
 * with white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Creditmemo extends Saas_PrintedTemplate_Model_Variable_Abstract_Entity
{
    /**
     * Key for config
     *
     * @see Saas_PrintedTemplate_Model_Variable_Abstract::_setListsFromConfig()
     * @var string
     */
    protected $_type = 'creditmemo';

    /**
     * Constructor
     *
     * @see Saas_PrintedTemplate_Model_Template_Variable_Abstract::__construct()
     * @param Magento_Sales_Model_Order_Creditmemo $value Creditmemo
     */
    public function __construct(Magento_Sales_Model_Order_Creditmemo $value)
    {
        parent::__construct($value);
        $this->_setListsFromConfig($this->_type);
    }

    /**
     * Initializes variable
     * @see Saas_PrintedTemplate_Model_Variable_Abstract::_initVariable()
     */
    protected function _initVariable()
    {
        $value = $this->_value;

        if ($value->getGrandTotal() - $value->getTaxAmount() < 0) {
            $value->setGrandTotalExclTax(0);
        } else {
            $value->setGrandTotalExclTax($value->getGrandTotal() - $value->getTaxAmount());
        }

        if (!$value->hasSubtotalWithShipping()) {
            $value->setSubtotalWithShipping($value->getSubtotal() + $value->getShippingAmount());
        }

        if (!$value->hasShippingDiscountAmount()) {
            $value->setShippingDiscountAmount(
                $value->getShippingAmount() > 0 ? $value->getOrder()->getShippingDiscountAmount() : 0
            );
        }

        if (!$value->hasShippingTotal()) {
            $value->setShippingTotal($value->getShippingInclTax() - $value->getShippingDiscountAmount());
        }
    }
}
