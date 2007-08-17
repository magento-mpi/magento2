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
        $this->setId('customers_grid');
    }

    protected function _prepareCollection()
    {     
        
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname');
        
        $collection->getSelect()
            ->joinRight(array('tr' => 'tag_relation'), 'tr.customer_id=e.entity_id', array('taged' => 'count(tr.tag_relation_id)'))
            ->joinRight(array('t' => 'tag'), 't.tag_id=tr.tag_id', 'status')
            ->where('t.status='.Mage_Tag_Model_Tag::STATUS_APPROVED)
            ->group('tr.customer_id')
            ->order('taged DESC');     
        
        //echo $collection->getSelect()->__toString();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn('id', array(
            'header'    =>__('ID'),
            'width'     => '50px',
            'sortable'  => false,
            'index'     =>'entity_id'
        ));
        
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
        
        $this->addColumn('taged', array(
            'header'    =>__('Total Tags'),
            'sortable'  => false,
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
