<?php
/**
 * Adminhtml search report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('searchReportGrid');
        $this->setDefaultSort('search_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
       
        $collection = Mage::getResourceModel('catalogsearch/search_collection');
        $this->setCollection($collection);
      
        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('search_id', array(
            'header'    =>__('ID'),
            'width'     =>'50px',
            'filter'    =>false,
            'index'     =>'search_id'
        ));
        
        $this->addColumn('search_query', array(
            'header'    =>__('Search Query'),
            'filter'    =>false,
            'index'     =>'search_query'
        ));
        
        $this->addColumn('num_results', array(
            'header'    =>__('Results'),
            'width'     =>'50px',
            'align'     =>'right',
            'type'      =>'number',
            'index'     =>'num_results'
        ));    
        
        $this->addColumn('popularity', array(
            'header'    =>__('Hits'),
            'width'     =>'50px',
            'align'     =>'right',
            'type'      =>'number',
            'index'     =>'popularity'
        )); 
        
        return parent::_prepareColumns();
    }
}
