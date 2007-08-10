<?php
/**
 * Adminhtml invoice items grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Invoice_Create_Items extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('invoice_items_grid');
        $this->setTemplate('sales/invoice/create/items.phtml');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_item_collection')
            ->addAttributeToSelect('*')
            ->setOrderFilter(Mage::registry('sales_invoice')->getOrder()->getId())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getOrder()
    {
        return Mage::registry('sales_invoice')->getOrder();
    }

    public function getInvoice()
    {
        return Mage::registry('sales_invoice');
    }

    public function getItemData($orderItemId)
    {
        $data = $this->getInvoice()->getData();
        if (isset($data['items']) && isset($data['items'][$orderItemId])) {
            return $data['items'][$orderItemId];
        }
        return null;
   }

}
