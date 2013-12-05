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

namespace Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment;

use Mtf\Client\Element\Locator;

/**
 * Class CustomerGrid
 * Backend customer grid
 *
 * @package Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->editLink = '//td[@data-column="grid_segment_name"]';
    }
}
