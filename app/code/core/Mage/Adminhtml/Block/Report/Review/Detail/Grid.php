<?php
/**
 * Adminhtml report reviews product grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Review_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reviews_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {     
        
        $collection = Mage::getModel('review/review')->getProductCollection();
        
        $collection->getSelect()->__toString();
        
        $collection->getSelect()
            ->where('rt.entity_pk_value='.(int)$this->getRequest()->getParam('id'));
     
        $collection->getEntity()->setStore(0);
        
        $this->setCollection($collection);
                
        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn('nickname', array(
            'header'    =>__('Customer'),
            'width'     =>'100px',
            'index'     =>'nickname'
        ));
        
        $this->addColumn('title', array(
            'header'    =>__('Title'),
            'width'     =>'150px',
            'index'     =>'title'
        ));
        
        $this->addColumn('detail', array(
            'header'    =>__('Detail'),
            'width'     =>'70%',
            'index'     =>'detail'
        ));
        
        $this->addColumn('created_at', array(
            'header'    =>__('Created at'),
            'index'     =>'created_at'
        ));
        
        $this->setFilterVisibility(false);
                      
        return parent::_prepareColumns();
    }
}
