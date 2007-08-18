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

class Mage_Adminhtml_Block_Customer_Edit_Tab_View_Wishlist extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_view_wishlist_grid');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setEmptyText(__("There are no items in customer's wishlist at the moment"));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('wishlist/wishlist')->loadByCustomer(Mage::registry('current_customer'))->getItemCollection()
            ->addAttributeToSelect('name')
            ->addStoreData();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => __('Product ID'),
            'index'     => 'product_id',
            'type'      => 'number',
            'width'     => '100px'
        ));

        $this->addColumn('product_name', array(
            'header'    => __('Product Name'),
            'index'     => 'name'
        ));

        $stores = Mage::getResourceModel('core/store_collection')->setWithoutDefaultFilter()->load()->toOptionHash();

        $this->addColumn('store', array(
            'header'    => __('Added From'),
            'index'     => 'store_id',
            'type' => 'options',
            'options' => $stores,
            'width'     => '160px',
        ));

        $this->addColumn('added_at', array(
            'header'    => __('Date Added'),
            'index'     => 'added_at',
            'type'      => 'date',
            'width'     => '140px',
        ));

        $this->addColumn('days', array(
            'header'    => __('Days in Wishlist'),
            'index'     => 'days_in_wishlist',
            'type'      => 'number',
            'width'     => '140px',
        ));

        return parent::_prepareColumns();
    }

    public function getHeadersVisibility()
    {
        return ($this->getCollection()->getSize() > 0);
    }

}
