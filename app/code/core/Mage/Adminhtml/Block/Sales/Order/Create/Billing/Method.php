<?php
/**
 * Adminhtml sales order create payment method block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_billing_method');
    }

    public function getHeaderText()
    {
        return __('Payment Method');
    }

    public function getHeaderCssClass()
    {
        return 'head-payment-method';
    }

    protected function _initChildren()
    {
        $this->setChild('form', $this->getLayout()->createBlock('adminhtml/sales_order_create_billing_method_form'));
        return parent::_initChildren();
    }

}
