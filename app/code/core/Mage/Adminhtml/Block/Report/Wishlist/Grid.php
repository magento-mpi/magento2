<?php
/**
 * Adminhtml wishlist report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Wishlist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{  
    public function __construct()
    {
        parent::__construct();
        $this->setId('wishlistReportGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
       
        $collection = Mage::getResourceModel('reports/wishlist_product_collection')
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name')
            ->addWishlistCount();
       
        $this->setCollection($collection);
      
        parent::_prepareCollection();

        return $this;
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id'
        ));
        
        $this->addColumn('name', array(
            'header'    =>__('Name'),
            'index'     =>'name'
        ));    
        
        $this->addColumn('wishlists', array(
            'header'    =>__('Wishlists'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'wishlists'
        ));
        
        $this->addColumn('bought_from_wishlists', array(
            'header'    =>__('Bought from wishlists'),
            'width'     =>'50px',
            'align'     =>'right',
            'sortable'  =>false,
            'index'     =>'bought_from_wishlists'
        ));
        
        $this->addColumn('w_vs_order', array(
            'header'    =>__('Wishlist vs. Regular Order'),
            'width'     =>'50px',
            'align'     =>'right',
            'sortable'  =>false,
            'index'     =>'w_vs_order'
        ));
        
        $this->addColumn('num_deleted', array(
            'header'    =>__('Number of times deleted'),
            'width'     =>'50px',
            'align'     =>'right',
            'sortable'  =>false,
            'index'     =>'num_deleted'
        ));
        
        $this->setFilterVisibility(false);
              
        return parent::_prepareColumns();
    }
}
