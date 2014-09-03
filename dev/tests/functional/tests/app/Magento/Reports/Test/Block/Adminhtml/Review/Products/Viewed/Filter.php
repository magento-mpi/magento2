<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Review\Products\Viewed;

use Mtf\ObjectManager;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Form;

/**
 * Class Filter
 * Filter for Product Views Report
 */
class Filter extends Form
{
    /**
     * Filter locator
     *
     * @var string
     */
    protected $filter = '[name="%s"]';

    /**
     * Mapping for fields in Account Report Grid
     *
     * @var array
     */
    protected $dataMapping = [
        'from' => 'datepicker',
        'to' => 'datepicker',
        'period_type' => 'select',
        'show_empty_rows' => 'select',
    ];

    /**
     * Search products in report grid
     *
     * @var array $productsReport
     * @return void
     */
    public function viewsReport(array $viewsReport)
    {
        $viewsReport = $this->prepareData($viewsReport);
        foreach ($viewsReport as $name => $value) {
            $this->_rootElement->find(sprintf($this->filter, $name), Locator::SELECTOR_CSS, $this->dataMapping[$name])
                ->setValue($value);
        }
    }

    /**
     * Prepare data
     *
     * @param array $viewsReport
     * @return array
     */
    protected function prepareData(array $viewsReport)
    {
        foreach ($viewsReport as $name => $reportFilter) {
            if ($name === 'period_type' || $name === 'show_empty_rows') {
                continue;
            }
            $date = ObjectManager::getInstance()->create(
                '\Magento\Backend\Test\Fixture\Date',
                ['params' => [], 'data' => ['pattern' => $reportFilter]]
            );
            $viewsReport[$name] = $date->getData();
        }
        return $viewsReport;
    }
}
