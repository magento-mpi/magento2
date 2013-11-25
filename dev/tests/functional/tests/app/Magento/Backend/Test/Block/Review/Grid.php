<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Review;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridAbstract;

/**
 * Reviews grid
 *
 * @package Magento\Backend\Test\Block\Review
 */
class Grid extends GridAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'id' => array(
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
