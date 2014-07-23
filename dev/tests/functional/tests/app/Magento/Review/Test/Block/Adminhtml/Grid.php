<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as GridAbstract;

/**
 * Class Grid
 * Reviews grid
 */
class Grid extends GridAbstract
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'review_id' => [
            'selector' => '#reviwGrid_filter_review_id',
        ],
        'title' => [
            'selector' => '#reviwGrid_filter_title',
        ],
        'status' => [
            'selector' => '#reviwGrid_filter_status',
            'input' => 'select',
        ],
    ];

    /**
     * Review actions
     *
     * @param string $reviewGridActions
     * @param array $items
     * @param string $reviewGridStatus
     * @return void
     */
    public function actions($reviewGridActions, array $items, $reviewGridStatus)
    {
        if ($reviewGridActions == 'Delete') {
            $this->delete($items);
        } else {
            $this->massaction($reviewGridActions, $items, $reviewGridStatus);
        }
    }
}
