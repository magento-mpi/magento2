<?php
/**
 * Adminhtml reviews by customers report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Review_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customers_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {     
        
        $collection = Mage::getResourceModel('customer/customer_collection')
                     ->addAttributeToSelect('entity_id')
                     ->addAttributeToSelect('firstname')
                     ->addAttributeToSelect('lastname');
            
        $collection->getSelect()->from('review_detail', 'count(review_detail.review_id) as review_cnt')
                                ->where('review_detail.customer_id=e.entity_id')
                                ->group('review_detail.customer_id');
                                               
                                               
        //echo $collection->getSelect()->__toString();
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
        
        $this->addColumn('firstname', array(
            'header'    =>__('First Name'),
            'width'     =>'100px',
            'index'     =>'firstname'
        ));    
        
        $this->addColumn('lastname', array(
            'header'    =>__('Last Name'),
            'width'     =>'100px',
            'index'     =>'lastname'
        ));
        
        $this->addColumn('review_cnt', array(
            'header'    =>__('Number Of Reviews'),
            'width'     =>'40px',
            'sortable'  =>false,
            'index'     =>'review_cnt'
        ));
        
        $this->setFilterVisibility(false);
                      
        return parent::_prepareColumns();
    }
   
}
