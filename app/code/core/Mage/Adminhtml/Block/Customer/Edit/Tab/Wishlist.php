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
        	->addWebsiteData();
            
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
        	'header'	=> __('id'),
        	'index'		=> 'product_id',
        	'type'		=> 'number',
        	'width'		=> '20px'
        ));
        
        $this->addColumn('product name', array(
        	'header'	=> __('Product name'),
        	'index'		=> 'name'
        ));
        
        $this->addColumn('price', array(
        	'header'	=> __('Product name'),
        	'index'		=> 'name'
        ));

        
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/*/index', array('_current'=>true));
    }

}
