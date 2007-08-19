<?php
/**
 * Adminhtml customer orders grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Cart extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_cart_grid');
        $this->setUseAjax(true);
        $this->_parentTemplate = $this->getTemplateName();
        $this->setTemplate('customer/tab/cart.phtml');
    }
    
    protected function _prepareCollection()
    {
        $quote = Mage::getResourceModel('sales/quote_collection')
            ->loadByCustomerId(Mage::registry('current_customer')->getId());

        if ($quote) {
            $collection = $quote->getItemsCollection(false);
        }
        else {
            $collection = new Varien_Data_Collection();
        }

        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header' => __('Product ID'),
            'index' => 'product_id',
            'width' => '100px',
        ));

        $this->addColumn('name', array(
            'header' => __('Product Name'),
            'index' => 'name',
        ));
        
        $this->addColumn('sku', array(
            'header' => __('SKU'),
            'index' => 'sku',
            'width' => '100px',
        ));
        
        $this->addColumn('qty', array(
            'header' => __('Qty'),
            'index' => 'qty',
            'type'  => 'number',
            'width' => '60px',
        ));
        
        $this->addColumn('price', array(
            'header' => __('Price'),
            'index' => 'price',
            'type'  => 'currency',
            'currency_code' => (string) Mage::getStoreConfig('general/currency/base'),
        ));
        
        $this->addColumn('total', array(
            'header' => __('Total'),
            'index' => 'row_total',
            'type'  => 'currency',
            'currency_code' => (string) Mage::getStoreConfig('general/currency/base'),
        ));

        $this->addColumn('action', array(
            'header'    => __('Action'),
            'index'     => 'quote_item_id',
            'type'      => 'action',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(
                array(
                    'caption' =>  __('Delete'),
                    'url'     =>  '#',
                    'onclick' =>  'return cartControl.removeItem($entity_id);'
                )
            )
        ));
        
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/*/cart', array('_current'=>true));
    }
    
    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }
    
}
