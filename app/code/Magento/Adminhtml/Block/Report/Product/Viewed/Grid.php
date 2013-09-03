<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml most viewed products report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Product_Viewed_Grid extends Magento_Adminhtml_Block_Report_Grid_Abstract
{
    /**
     * Column for grid to be grouped by
     *
     * @var string
     */
    protected $_columnGroupBy = 'period';

    /**
     * Grid resource collection name
     *
     * @var string
     */
    protected $_resourceCollectionName  = 'Magento_Reports_Model_Resource_Report_Product_Viewed_Collection';

    /**
     * Init grid parameters
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
    }

    /**
     * Custom columns preparation
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'        => __('Interval'),
            'index'         => 'period',
            'width'         => 100,
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'Magento_Adminhtml_Block_Report_Sales_Grid_Column_Renderer_Date',
            'totals_label'  => __('Total'),
            'html_decorators' => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('product_name', array(
            'header'    => __('Product'),
            'index'     => 'product_name',
            'type'      => 'string',
            'sortable'  => false,
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name'
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('product_price', array(
            'header'        => __('Price'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'product_price',
            'sortable'      => false,
            'rate'          => $this->getRate($currencyCode),
            'header_css_class'  => 'col-price',
            'column_css_class'  => 'col-price'
        ));

        $this->addColumn('views_num', array(
            'header'    => __('Views'),
            'index'     => 'views_num',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false,
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));


        $this->addExportType('*/*/exportViewedCsv', __('CSV'));
        $this->addExportType('*/*/exportViewedExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Don't use orders in collection
     *
     * @param Magento_Reports_Model_Resource_Report_Collection_Abstract $collection
     * @param \Magento\Object $filterData
     * @return Magento_Adminhtml_Block_Report_Grid_Abstract
     */
    protected function _addOrderStatusFilter($collection, $filterData)
    {
        return $this;
    }
}
