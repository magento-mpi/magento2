<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit\Tab\GiftRegistry;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Grid on GiftRegistry tab
 */
class Grid extends GridInterface
{
    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td.col-title';

    /**
     * Grid fields map
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => '#customerGrid_filter_title'
        ],
        'registrants' => [
            'selector' => '#customerGrid_filter_registrants'
        ],
    ];
}
