<?php
/**
 * Adminhtml tags detail for customer report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
 
class Mage_Adminhtml_Block_Report_Tag_Customer_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customers_grid');
    }

    protected function _prepareCollection()
    {     
        
        $collection = Mage::getModel('tag/tag')
            ->getEntityCollection()
            ->addCustomerFilter($this->getRequest()->getParam('id'))
            ->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus())
            ->setDescOrder('DESC');
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn('name', array(
            'header'    =>__('Product Name'),
            'sortable'  => false,
            'index'     =>'name'
        ));
        
        $this->addColumn('tag_name', array(
            'header'    =>__('Tag Name'),
            'sortable'  => false,
            'index'     =>'tag_name'
        ));
        
        $this->addColumn('created_at', array(
            'header'    =>__('Added'),
            'sortable'  => false,
            'index'     =>'created_at'
        ));        
     
        $this->setFilterVisibility(false);
        
        return parent::_prepareColumns();
    }
}
