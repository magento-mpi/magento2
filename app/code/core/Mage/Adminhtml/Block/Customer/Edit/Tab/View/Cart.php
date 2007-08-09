<?php
/**
 * Adminhtml customer cart items grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Edit_Tab_View_Cart extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_view_cart_grid');
        $this->setDefaultSort('added_at', 'desc');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setEmptyText(__("There are no items in customer's shopping cart at the moment"));
    }

    protected function _prepareCollection()
    {
        // TODO
        /*
        $collection = Mage::getResourceModel('sales/quote_item_collection')
            ->addAttributeToFilter('customer_id', Mage::registry('customer')->getId());
        */
        $collection = new Varien_Data_Collection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('product_id', array(
            'header' => __('Product ID'),
            'index' => 'product_id',
        ));

        $this->addColumn('product_name', array(
            'header' => __('Product Name'),
            'index' => 'product_name',
        ));

        $this->addColumn('qty', array(
            'header' => __('Qty'),
            'index' => 'qty',
        ));

        $this->addColumn('added_at', array(
            'header' => __('Added at'),
            'index' => 'added_at',
            'type' => 'datetime',
        ));

        $stores = Mage::getResourceModel('core/store_collection')->setWithoutDefaultFilter()->load()->toOptionHash();

        $this->addColumn('store_id', array(
            'header' => __('Added In'),
            'index' => 'store_id',
            'type' => 'options',
            'options' => $stores,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        // TODO
        return Mage::getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }

    public function getHeadersVisibility()
    {
        return ($this->getCollection()->getSize() > 0);
    }

}
