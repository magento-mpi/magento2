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
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {     
        
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('name');
        
        $collection->getSelect()
            ->joinLeft(array('tr' => 'tag_relation'), 'e.entity_id=tr.product_id')
            ->joinLeft(array('t' => 'tag'), 't.tag_id=tr.tag_id', array('tag_name' => 'name'))
            ->where('tr.customer_id='.$this->getRequest()->getParam('id'))
            ->where('t.status='.Mage_Tag_Model_Tag::STATUS_APPROVED)
            ->order('t.tag_id DESC');
        
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
        
        $this->addColumn('tag_name', array(
            'header'    =>__('Tag Name'),
            'width'     => '250px',
            'sortable'  => false,
            'index'     =>'tag_name'
        ));
        
        
               
        $this->setFilterVisibility(false);
        
        return parent::_prepareColumns();
    }
}
