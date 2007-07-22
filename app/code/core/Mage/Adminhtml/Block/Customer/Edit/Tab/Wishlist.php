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
class Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('wishlistGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('wishlist/wishlist')->loadByCustomer(Mage::registry('customer'))->getItemCollection()
        	->addAttributeToSelect('name')
        	->addAttributeToSelect('price')
        	->addAttributeToSelect('small_image')
        	->addStoreData();
            
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    
    
    protected function _prepareColumns()
    {
    	
        $this->addColumn('id', array(
        	'header'	=> __('Id'),
        	'index'		=> 'product_id',
        	'type'		=> 'number',
        	'width'		=> '15px'
        ));
        
        $this->addColumn('product_name', array(
        	'header'	=> __('Product name'),
        	'index'		=> 'name'
        ));
        
        $this->addColumn('description', array(
        	'header'	=> __('User description'),
        	'index'		=> 'description',
        	'renderer'	=> 'adminhtml/customer_edit_tab_wishlist_grid_renderer_description'
        ));
        
        // Collection for stores filters
        if(!$collection = Mage::registry('stores_select_collection')) {
			$collection =  Mage::getResourceModel('core/store_collection')
				->load();
			Mage::register('stores_select_collection', $collection);
		}
        
        $this->addColumn('store', array(
        	'header'	=> __('Added From'),
        	'index'		=> 'store_name',
        	'filter'	=> 'adminhtml/customer_edit_tab_wishlist_grid_filter_store'
        ));
        
        $this->addColumn('visible_in', array(
        	'header'	=> __('Visible In'),
        	'index'		=> 'store_id',
        	'filter'	=> 'adminhtml/customer_edit_tab_wishlist_grid_filter_visible',
        	'renderer'	=> 'adminhtml/customer_edit_tab_wishlist_grid_renderer_visible'
        ));
        
        $this->addColumn('added_at', array(
        	'header'	=> __('Added At'),
        	'index'		=> 'added_at',
        	'type'		=> 'date'
        ));
        
        $this->addColumn('days', array(
        	'header'	=> __('In wishlist'),
        	'index'		=> 'days_in_wishlist',
        	'type'		=> 'number'
        ));


        
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/*/wishlist', array('_current'=>true));
    }
    
    
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getFilter()->getValue()) {
            $this->getCollection()->addAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());
        }
        
        return $this;
    }

}
