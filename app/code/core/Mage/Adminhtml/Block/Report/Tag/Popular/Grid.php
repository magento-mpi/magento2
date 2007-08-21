<?php
/**
 * Adminhtml popular tags report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Tag_Popular_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {     
        
        $collection = Mage::getResourceModel('reports/tag_collection')
            ->addGroupByTag()
            ->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus());
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>__('Tag Name'),
            'sortable'  =>false,
            'index'     =>'name'
        ));    
        
        $this->addColumn('taged', array(
            'header'    =>__('Number of Use'),
            'width'     =>'50px',
            'align'     =>'right',
            'sortable'  =>false,
            'index'     =>'taged'
        ));
         
        $this->setFilterVisibility(false); 
                      
        return parent::_prepareColumns();
    }
       
    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/tagDetail', array('id'=>$row->tag_id));
    }
    
}
