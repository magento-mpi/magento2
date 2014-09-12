<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Review\Products\Viewed;

use Mtf\Block\Form;
use Mtf\ObjectManager;

/**
 * Class Filter
 * Filter for Product Views Report
 */
class Filter extends Form
{
    /**
     * Search products in report grid
     *
     * @var array $productsReport
     * @return void
     */
    public function viewsReport(array $viewsReport)
    {
        $viewsReport = $this->prepareData($viewsReport);
        $data = $this->dataMapping($viewsReport);
        $this->_fill($data);
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
