<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Test\Block;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;
use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Admin logging grid
 */
class Grid extends GridInterface
{
    /**
     * Admin action log report grid filters
     *
     * @var array
     */
    protected $filters = [
        'timeFrom' => [
            'selector' => ''
        ],
        'timeTo' => [
            'selector' => ''
        ],
        'actionGroup' => [
            'selector' => '#loggingLogGrid_filter_event',
            'input' => 'select'
        ],
        'action' => [
            'selector' => '#loggingLogGrid_filter_action',
            'input' => 'select'
        ],
        'ipAddress' => [
            'selector' => '#loggingLogGrid_filter_ip'
        ],
        'username' => [
            'selector' => '#loggingLogGrid_filter_user',
            'input' => 'select'
        ],
        'result' => [
            'selector' => '#loggingLogGrid_filter_status',
            'input' => 'select'
        ],
        'fullActionName' => [
            'selector' => '#loggingLogGrid_filter_fullaction'
        ],
        'shortDetails' => [
            'selector' => '#loggingLogGrid_filter_info',
        ],
    ];

    /**
     * Element locator to select first entity in grid
     *
     * @var string
     */
    protected $firstViewLink = "#loggingLogGrid_table tr:first-child [data-column='view'] > a";

    /**
     * Search and open first View link in grid
     *
     * @param array $filter
     * @throws \Exception
     */
    public function searchSortAndOpen(array $filter)
    {
        $this->search($filter);
        $sortLink = $this->_rootElement->find("[name='time'][title='asc']");
        if ($sortLink->isVisible()) {
            $this->sortGridByField('time', 'asc');
            $this->getTemplateBlock()->waitLoader();
            $this->sortGridByField('time');
            $this->getTemplateBlock()->waitLoader();
        } else {
            $this->sortGridByField('time');
            $this->getTemplateBlock()->waitLoader();
        }
        $rowItem = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_CSS);
        if ($rowItem->isVisible()) {
            $rowItem->find($this->firstViewLink, Locator::SELECTOR_CSS)->click();
            $this->getTemplateBlock()->waitLoader();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }
}
