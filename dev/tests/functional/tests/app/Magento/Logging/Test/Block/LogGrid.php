<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Test\Block;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

/**
 * Class ProductGrid
 * Backend catalog product grid
 */
class LogGrid extends Grid
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
    protected $lastViewLink = '#loggingLogGrid_table > tbody > tr:first-child > td.col-view.last:last-child > a';

    /**
     * Click first View link in grid
     */
    public function clickViewLink()
    {
        $this->_rootElement->find($this->lastViewLink, Locator::SELECTOR_CSS)->click();
    }
}
