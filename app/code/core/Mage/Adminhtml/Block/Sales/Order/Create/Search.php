<?php
/**
 * Adminhtml sales order create search block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Search extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_search');
    }

    public function getHeaderText()
    {
        return __('Please select products to add');
    }

    protected function _initChildren()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/sales_order_create_search_grid'));
        return parent::_initChildren();
    }

    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => __('Add Selected Product to Order'),
            'onclick' => 'sc_searchAdd()',
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }

    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }

}
