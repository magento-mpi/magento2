<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Sales order grid
 */
class Grid extends GridInterface
{
    /**
     * 'Add New' order button
     *
     * @var string
     */
    protected $addNewOrder = "../*[@class='page-actions']//*[@id='add']";

    /**
     * Purchase Point Filter selector
     *
     * @var string
     */
    protected $purchasePointFilter = '//*[@data-ui-id="widget-grid-column-filter-store-filter-store-id"]';

    /**
     * Purchase Point Filter option group elements selector
     *
     * @var string
     */
    protected $purchasePointOptionGroup = '//*[@data-ui-id="widget-grid-column-filter-store-filter-store-id"]/optgroup';

    /**
     * Order Id td selector
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-action] a';

    /**
     * {@inheritdoc}
     */
    protected $filters = [
        'id' => [
            'selector' => 'input[name="real_order_id"]'
        ],
        'status' => [
            'selector' => 'select[name="status"]',
            'input' => 'select'
        ],
    ];

    /**
     * Start to create new order
     */
    public function addNewOrder()
    {
        $this->_rootElement->find($this->addNewOrder, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get selected data from Purchase Point filter
     *
     * @return string
     */
    public function getPurchasePointFilterText()
    {
        return $this->_rootElement->find($this->purchasePointFilter, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Assert the number of Purchase Point Filter option group elements by checking non-existing group
     *
     * @param $number
     * @return bool
     */
    public function assertNumberOfPurchasePointFilterOptionsGroup($number)
    {
        $selector = $this->purchasePointOptionGroup . '[' . ($number + 1) . ']';
        return !$this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible();
    }
}
