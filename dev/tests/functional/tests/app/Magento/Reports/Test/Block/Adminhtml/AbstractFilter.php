<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml;

use Mtf\Block\Form;
use Mtf\ObjectManager;

/**
 * Abstract Class Filter
 * Filter for Report
 */
abstract class AbstractFilter extends Form
{
    /**
     * Date fields
     *
     * @var array
     */
    protected $dateFields = ['from', 'to'];

    /**
     * Prepare data
     *
     * @param array $viewsReport
     * @return array
     */
    protected function prepareData(array $viewsReport)
    {
        foreach ($viewsReport as $key => $reportFilter) {
            if (in_array($key, $this->dateFields)) {
                $date = ObjectManager::getInstance()->create(
                    '\Magento\Backend\Test\Fixture\Date',
                    ['params' => [], 'data' => ['pattern' => $reportFilter]]
                );
                $viewsReport[$key] = $date->getData();
            }
        }
        return $viewsReport;
    }

    /**
     * Search entity in report grid
     *
     * @var array $report
     * @return void
     */
    public function viewsReport(array $report)
    {
        $report = $this->prepareData($report);
        $data = $this->dataMapping($report);
        $this->_fill($data);
    }
}
