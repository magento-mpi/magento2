<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment;

use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Backend customer segment grid
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
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-grid_segment_name]';

    /**
     * {@inheritdoc}
     */
    protected $filters = [
        'grid_segment_name' => [
            'selector' => 'input[name="grid_segment_name"]',
        ],
        'grid_segment_is_active' => [
            'selector' => 'select[name="grid_segment_is_active"]',
            'input' => 'select',
        ],
        'grid_segment_website' => [
            'selector' => 'select[name="grid_segment_website"]',
            'input' => 'select',
        ],
    ];

    /**
     * Add new segment
     */
    public function addNewSegment()
    {
        $this->_rootElement->find($this->addNewSegment, Locator::SELECTOR_XPATH)->click();
    }
}
