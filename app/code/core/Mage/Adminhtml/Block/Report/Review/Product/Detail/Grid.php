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
 
class Mage_Adminhtml_Block_Report_Tag_Product_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customers_grid');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {     
        
        $collection = Mage::getResourceModel('tag/tag_collection');
        
        $collection->getSelect()
            ->joinLeft(array('tr' => 'tag_relation'), 'main_table.tag_id=tr.tag_id', array('tag_total' => 'count(tr.tag_relation_id)'))
            ->where('tr.product_id='.$this->getRequest()->getParam('id'))
            ->where('main_table.status='.Mage_Tag_Model_Tag::STATUS_APPROVED)
            ->group('tr.tag_id')
            ->order('tag_total DESC');
        
        //echo $collection->getSelect()->__toString();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn('name', array(
            'header'    =>__('Product Name'),
            'width'     => '250px',
            'sortable'  => false,
            'index'     =>'name'
        ));
        
        $this->addColumn('tag_total', array(
            'header'    =>__('Tag Name'),
            'width'     => '250px',
            'sortable'  => false,
            'index'     =>'tag_total'
        ));
        
        
               
        $this->setFilterVisibility(false);
        
        return parent::_prepareColumns();
    }
}
