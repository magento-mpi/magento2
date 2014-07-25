<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class SalesOrderView
 * Order view page
 *
 */
class SalesOrderView extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'sales/order/view';

    /**
     * Sales order grid
     *
     * @var string
     */
    protected $orderTotalsBlock = '.order-totals';

    /**
     * Sales order grid
     *
     * @var string
     */
    protected $orderHistoryBlock = '.order-comments-history';

    /**
     * Account information block
     *
     * @var string
     */
    protected $infoBlock = '.order-account-information';

    /**
     * Page title block
     *
     * @var string
     */
    protected $titleBlock = '.page-title .title';

    /**
     * Items ordered grid
     *
     * @var string
     */
    protected $itemsOrderedBlock = '#sales_order_view_tabs_order_info_content .grid';

    /**
     * Order information block
     *
     * @var string
     */
    protected $orderInfoBlock = '[data-ui-id="sales-order-tabs-tab-content-order-info"]';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Returns the items ordered block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\View\Items
     */
    public function getItemsOrderedBlock()
    {
        return $this->itemsOrderedBlock = Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderViewItems(
            $this->_browser->find($this->itemsOrderedBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get order totals block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Totals
     */
    public function getOrderTotalsBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderTotals(
            $this->_browser->find($this->orderTotalsBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get order history block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\History
     */
    public function getOrderHistoryBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderHistory(
            $this->_browser->find($this->orderHistoryBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get order information block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\View\Info
     */
    public function getInformationBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderViewInfo(
            $this->_browser->find($this->infoBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get page title block
     *
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return Factory::getBlockFactory()->getMagentoThemeHtmlTitle(
            $this->_browser->find($this->titleBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get order information block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\View\Tab\Info
     */
    public function getOrderInfoBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderViewTabInfo(
            $this->_browser->find($this->orderInfoBlock, Locator::SELECTOR_CSS)
        );
    }
}
