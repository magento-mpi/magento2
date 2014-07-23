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
     * Selector for action select
     *
     * @var string
     */
    protected $action = '#reviwGrid_massaction-select';

    /**
     * Selector for status select
     *
     * @var string
     */
    protected $status = '#status';

    /**
     * Review actions
     *
     * @param string $reviewGridActions
     * @param string $reviewGridStatus
     * @param bool $acceptAlert [optional]
     * @return void
     */
    public function actions($reviewGridActions, $reviewGridStatus, $acceptAlert = false)
    {
        $this->_rootElement->find($this->action, Locator::SELECTOR_CSS, 'select')->setValue($reviewGridActions);
        if ($reviewGridStatus != '-') {
            $this->_rootElement->find($this->status, Locator::SELECTOR_CSS, 'select')->setValue($reviewGridStatus);
        }
        if ($reviewGridActions == 'Delete') {
            $acceptAlert = true;
        }
        $this->massActionSubmit($acceptAlert);
    }
}
