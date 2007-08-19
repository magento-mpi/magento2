<?php
/**
 * Adminhtml customer view wishlist block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Edit_Tab_View_Sales extends Mage_Core_Block_Template
{

    /**
     * Enter description here...
     *
     * @var Mage_Sales_Model_Entity_Sale_Collection
     */
    protected $_collection;

    /**
     * Enter description here...
     *
     * @var Mage_Directory_Model_Currency
     */
    protected $_currency;

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_view_sales_grid');
        $this->setTemplate('customer/tab/view/sales.phtml');
    }

    public function _beforeToHtml()
    {
        $this->_currency = Mage::getModel('directory/currency')
            ->load(Mage::getStoreConfig('general/currency/base'))
        ;

        $this->_collection = Mage::getResourceModel('sales/sale_collection')
            ->setCustomerFilter(Mage::registry('current_customer'))
            ->load()
        ;

        return parent::_beforeToHtml();
    }

    public function getRows()
    {
        return $this->_collection->getItems();
    }

    public function getTotals()
    {
        return $this->_collection->getTotals();
    }

    public function getPriceFormatted($price)
    {
        return $this->_currency->format($price);
    }

}
