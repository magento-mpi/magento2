<?php
/**
 * Adminhtml sales order create coupons form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Coupons_Form extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_coupons_form');
        $this->setTemplate('sales/order/create/coupons/form.phtml');
    }

    public function getCouponCode()
    {
        return $this->getParentBlock()->getQuote()->getCouponCode();
    }

}
