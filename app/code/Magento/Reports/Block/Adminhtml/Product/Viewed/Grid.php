<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Product\Viewed;

/**
 * Adminhtml most viewed products report grid block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid
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
    protected $_resourceCollectionName  = 'Magento\Reports\Model\Resource\Report\Product\Viewed\Collection';

    /**
     * Init grid parameters
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
    }

    /**
     * Custom columns preparation
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'        => __('Interval'),
            'index'         => 'period',
            'width'         => 100,
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
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
     * @param \Magento\Reports\Model\Resource\Report\Collection\AbstractCollection $collection
     * @param \Magento\Object $filterData
     * @return \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _addOrderStatusFilter($collection, $filterData)
    {
        return $this;
    }
}
