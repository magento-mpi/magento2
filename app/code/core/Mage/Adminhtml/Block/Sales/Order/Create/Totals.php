<?php
/**
 * Adminhtml sales order create totals block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Totals extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_totals');
        $this->setTemplate('sales/order/create/totals.phtml');
    }

    public function getTotals()
    {
        return $this->getQuote()->getTotals();
    }

    public function toHtml()
    {
        if (! $this->getSession()->getStore()) {
            return '';
        }
        return parent::toHtml();
    }

}
