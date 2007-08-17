<?php
/**
 * Adminhtml sales order create sidebar cart block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Cart extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_sidebar_cart');
        $this->setScId('cart');
    }

    protected function _prepareItems()
    {
        // customer's front-end quote
    	if (is_null($this->_items) && ($quote = $this->getSession()->getCustomerQuote())) {
    	    /* @var $quote Mage_Sales_Model_Quote */
            $this->setItems($quote->getAllItems());
    	}
        return $this;
    }

    public function getHeaderText()
    {
        return __('Shopping Cart');
    }

}
