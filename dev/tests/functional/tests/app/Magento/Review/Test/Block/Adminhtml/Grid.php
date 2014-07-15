<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml;

use Mtf\Client\Element;
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
    protected $filters = array(
        'review_id' => array(
            'selector' => '#reviwGrid_filter_review_id',
        ),
        'title' => array(
            'selector' => '#reviwGrid_filter_title',
        ),
        'status' => array(
            'selector' => '#reviwGrid_filter_status',
            'input' => 'select',
        ),
    );
}
