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
 * Class Grid
 * Backend customer segment grid
 *
 * @package Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * 'Add New' segment button
     *
     * @var string
     */
    protected $addNewSegment = "//button[@id='add']";

    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'grid_segment_name' => array(
            'selector' => '#customersegmentGrid_filter_grid_segment_name'
        )
    );

    /**
     * Add new segment
     */
    public function addNewSegment()
    {
        $this->_rootElement->find($this->addNewSegment, Locator::SELECTOR_XPATH)->click();
    }
}
