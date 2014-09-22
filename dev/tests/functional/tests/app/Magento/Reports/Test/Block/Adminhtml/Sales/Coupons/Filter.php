<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Sales\Coupons;

use Mtf\Block\Form;
use Mtf\ObjectManager;

/**
 * Class Filter
 * Filter for Coupons Views Report
 */
class Filter extends Form
{
    /**
     * Search coupons in report grid
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
            if ($reportFilter == '-') {
                unset($viewsReport[$name]);
            }
            if ($name === 'from' || $name === 'to') {
                $date = ObjectManager::getInstance()->create(
                    '\Magento\Backend\Test\Fixture\Date',
                    ['params' => [], 'data' => ['pattern' => $reportFilter]]
                );
                $viewsReport[$name] = $date->getData();
            }
        }
        return $viewsReport;
    }
}
