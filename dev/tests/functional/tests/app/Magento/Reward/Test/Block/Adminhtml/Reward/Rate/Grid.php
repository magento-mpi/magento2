<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Adminhtml\Reward\Rate;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;

/**
 * Class Grid
 * Reward rate grid
 */
class Grid extends ParentGrid
{
    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td[data-column="rate"]';

    /**
     * Edit link selector
     *
     * @var string
     */
    protected $editLink = 'td[data-column="rate_id"]';

    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'rate_id' => [
            'selector' => 'input[name="rate_id"]',
        ],
        'website_id' => [
            'selector' => 'select[name="website_id"]',
            'input' => 'select',
        ],
        'customer_group_id' => [
            'selector' => 'select[name="customer_group_id"]',
            'input' => 'select'
        ],
    ];

    /**
     * First row selector
     *
     * @var string
     */
    protected $firstRowSelector = '[data-role="row"] td[data-column="rate_id"]';

    /**
     * Check whether first row is visible
     *
     * @return bool
     */
    public function isFirstRowVisible()
    {
        return $this->_rootElement->find($this->firstRowSelector)->isVisible();
    }

    /**
     * Open first item in grid
     *
     * @return void
     */
    public function openFirstRow()
    {
        $this->_rootElement->find($this->firstRowSelector)->click();
    }
}
