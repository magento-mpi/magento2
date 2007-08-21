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
    }

    protected function _prepareCollection()
    {     

        $collection = Mage::getResourceModel('reports/review_product_collection');
       
        $collection->getEntity()->setStore(0);
      
        $this->setCollection($collection);

        return parent::_prepareCollection();
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
            'index'     =>'name'
        ));    
        
        $this->addColumn('review_cnt', array(
            'header'    =>__('Number of Reviews'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'review_cnt'
        ));
        
        $this->addColumn('avg_rating', array(
            'header'    =>__('Average rating'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'avg_rating'
        ));
        
        $this->addColumn('last_created', array(
            'header'    =>__('Last Review'),
            'width'     =>'150px',
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