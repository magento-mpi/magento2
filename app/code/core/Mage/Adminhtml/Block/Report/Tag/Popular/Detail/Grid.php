<?php
/**
 * Adminhtml tags detail for product report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
 
class Mage_Adminhtml_Block_Report_Tag_Popular_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_grid');
    }

    protected function _prepareCollection()
    {     
        
        $collection = Mage::getResourceModel('reports/tag_customer_collection')
                ->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus())
                ->addTagFilter($this->getRequest()->getParam('id'))
                ->addDescOrder();
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->addProductName();
    }
    
    protected function _prepareColumns()
    {
        
        $this->addColumn('firstname', array(
            'header'    =>__('First Name'),
            'sortable'  => false,
            'index'     =>'firstname'
        ));
        
        $this->addColumn('lastname', array(
            'header'    =>__('Last Name'),
            'sortable'  => false,
            'index'     =>'lastname'
        ));
        
        $this->addColumn('product', array(
            'header'    =>__('Product Name'),
            'sortable'  => false,
            'index'     =>'product'
        ));
        
        $this->setFilterVisibility(false);
        
        return parent::_prepareColumns();
    }
}