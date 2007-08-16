<?php
/**
 * Adminhtml sales order create select store block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Store extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_store');
    }

    protected function _initChildren()
    {
        $this->setChild('select', $this->getLayout()->createBlock('adminhtml/sales_order_create_store_select'));
        return parent::_initChildren();
    }

    public function getHeaderText()
    {
        return __('Please select a store');
    }

    public function toHtml()
    {
        if ($this->getSession()->getStoreId()) {
            return '';
        }
        return parent::toHtml();
    }

}
