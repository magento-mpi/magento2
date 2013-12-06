<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Backend;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class PageGrid
 * Backend cms page grid
 *
 * @package Magento\Cms\Test\Block\Backend
 */
class PageGrid extends Grid
{
    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'page_title' => array(
            'selector' => '#cmsPageGrid_filter_title'
        ),
    );
}
