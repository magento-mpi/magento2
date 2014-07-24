<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Adminhtml\Reward\Rate;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class Grid
 * Adminhtml Reward Exchange Rate management grid
 */
class Grid extends AbstractGrid
{
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
            'input' => 'select',
        ],
    ];

    /**
     * First row selector
     *
     * @var string
     */
    protected $firstRow = '[data-role="row"] td[data-column="rate_id"]';

    /**
     * Click on first exchange rate
     *
     * @return void
     */
    public function clickOnFirstRow()
    {
        if ($this->isFirstRowVisible()) {
            $this->_rootElement->find($this->firstRow)->click();
        }
    }

    /**
     * Is first row visible
     *
     * @return bool
     */
    public function isFirstRowVisible()
    {
        return $this->_rootElement->find($this->firstRow)->isVisible();
    }
}
