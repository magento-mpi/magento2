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

/**
 * Class ProductGrid
 * Backend catalog product grid
 *
 */
class LogGrid extends Grid
{
    /**
     * Admin action log report grid filters
     *
     * @var array
     */
    protected $filters = array(
        'timeFrom' => array(
            'selector' => ''
        ),
        'timeTo' => array(
            'selector' => ''
        ),
        'actionGroup' => array(
            'selector' => '#loggingLogGrid_filter_event',
            'input' => 'select'
        ),
        'action' => array(
            'selector' => '#loggingLogGrid_filter_action',
            'input' => 'select'
        ),
        'ipAddress' => array(
            'selector' => '#loggingLogGrid_filter_ip'
        ),
        'username' => array(
            'selector' => '#loggingLogGrid_filter_user',
            'input' => 'select'
        ),
        'result' => array(
            'selector' => '#loggingLogGrid_filter_status',
            'input' => 'select'
        ),
        'fullActionName' => array(
            'selector' => '#loggingLogGrid_filter_fullaction'
        ),
        'shortDetails' => array(
            'selector' => '#loggingLogGrid_filter_info',
        ),
    );
}
