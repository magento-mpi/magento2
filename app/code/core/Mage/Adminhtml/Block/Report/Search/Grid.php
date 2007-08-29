<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml search report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
