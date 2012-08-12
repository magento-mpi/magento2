<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml search report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize Grid Properties
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('searchReportGrid');
        $this->setDefaultSort('query_id');
        $this->setDefaultDir('desc');
    }

    /**
     * Prepare Search Report collection for grid
     *
     * @return Mage_Adminhtml_Block_Report_Search_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_CatalogSearch_Model_Resource_Query_Collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Report_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('query_id', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('ID'),
            'width'     =>'50px',
            'filter'    =>false,
            'index'     =>'query_id',
            'type'      =>'number'
        ));

        $this->addColumn('query_text', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Search Query'),
            'index'     =>'query_text'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('Mage_Catalog_Helper_Data')->__('Store'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_view'    => true,
                'sortable'      => false
            ));
        }

        $this->addColumn('num_results', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Results'),
            'width'     =>'50px',
            'align'     =>'right',
            'type'      =>'number',
            'index'     =>'num_results'
        ));

        $this->addColumn('popularity', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Hits'),
            'width'     =>'50px',
            'align'     =>'right',
            'type'      =>'number',
            'index'     =>'popularity'
        ));

        $this->addExportType('*/*/exportSearchCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportSearchExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve Row Click callback URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_search/edit', array('id' => $row->getId()));
    }
}

