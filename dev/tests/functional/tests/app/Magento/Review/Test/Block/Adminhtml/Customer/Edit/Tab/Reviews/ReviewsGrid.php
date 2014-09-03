<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml\Customer\Edit\Tab\Reviews;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class ReviewsGrid
 * Product reviews grid block
 */
class ReviewsGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'review_id' => [
            'selector' => 'input[name="review_id"]',
        ],
        'status' => [
            'selector' => 'select[name="status"]',
            'input' => 'select',
        ],
        'title' => [
            'selector' => 'input[name="title"]',
        ],
        'nickname' => [
            'selector' => 'input[name="nickname"]',
        ],
        'detail' => [
            'selector' => 'input[name="detail"]',
        ],
        'visible_in' => [
            'selector' => 'select[name="visible_in"]',
            'input' => 'selectstore',
        ],
        'type' => [
            'selector' => 'select[name="type"]',
            'input' => 'select',
        ],
        'name' => [
            'selector' => 'input[name="name"]',
        ],
        'sku' => [
            'selector' => 'input[name="sku"]',
        ],
    ];
}
