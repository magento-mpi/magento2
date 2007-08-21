<?php
/**
 * Adminhtml tags by customers report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
 
class Mage_Adminhtml_Block_Report_Tag_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {     
        
        $collection = Mage::getResourceModel('reports/tag_customer_collection');
        
        $collection->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus())
            ->addGroupByCustomer()
            ->addTagedCount();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn('id', array(
            'header'    =>__('ID'),
            'width'     => '50px',
            'align'     =>'right',
            'index'     =>'entity_id'
        ));
        
        $this->addColumn('firstname', array(
            'header'    =>__('First Name'),
            'index'     =>'firstname'
        ));
        
        $this->addColumn('lastname', array(
            'header'    =>__('Last Name'),
            'index'     =>'lastname'
        ));
        
        $this->addColumn('taged', array(
            'header'    =>__('Total Tags'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'taged'
        ));
        
        $this->setFilterVisibility(false);
        
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/customerDetail', array('id'=>$row->entity_id));
    } 
}
