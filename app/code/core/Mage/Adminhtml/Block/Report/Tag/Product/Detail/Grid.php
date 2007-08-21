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
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {     
        
        $collection = Mage::getResourceModel('reports/tag_product_collection');
        
        $collection->addTagedCount()
            ->addProductFilter($this->getRequest()->getParam('id'))
            ->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus())
            ->addGroupByTag();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn('tag_name', array(
            'header'    =>__('Tag Name'),
            'index'     =>'tag_name'
        ));
        
        $this->addColumn('taged', array(
            'header'    =>__('Tag use'),
            'index'     =>'taged'
        ));
        
        
               
        $this->setFilterVisibility(false);
        
        return parent::_prepareColumns();
    }
}
