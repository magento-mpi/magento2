<?php
/**
 * Adminhtml reviews by products report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Review_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridProducts');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {     
              
        $collection = Mage::getModel('review/review')->getProductCollection()
            ->addRateVotes();
        
        $collection->getSelect()
            ->from('', array('count(rt.entity_id) as review_cnt', 'max(rt.created_at) as last_created'))
            ->group('rt.entity_pk_value');
        
        $collection->getEntity()->setStore(0);
        
        $this->setCollection($collection);
                
        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn('entity_id', array(
            'header'    =>__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id'
        ));
        
        $this->addColumn('name', array(
            'header'    =>__('Product Name'),
            'width'     =>'200px',
            'index'     =>'name'
        ));    
        
        $this->addColumn('review_cnt', array(
            'header'    =>__('Number of Reviews'),
            'width'     =>'40px',
            'index'     =>'review_cnt'
        ));
        
        $this->addColumn('avg_rating', array(
            'header'    =>__('Average rating'),
            'width'     =>'40px',
            'index'     =>'avg_rating'
        ));
        
        $this->addColumn('last_created', array(
            'header'    =>__('Last Review'),
            'width'     =>'40px',
            'index'     =>'last_created'
        ));
         
        $this->setFilterVisibility(false); 
                      
        return parent::_prepareColumns();
    }
       
    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/productDetail', array('id'=>$row->entity_id));
    }
    
}
