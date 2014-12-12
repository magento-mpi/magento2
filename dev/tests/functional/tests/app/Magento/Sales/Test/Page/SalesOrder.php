<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\BackendPage;

/**
 * Class SalesOrder
 * Manage orders page
 */
class SalesOrder extends BackendPage
{
    /**
     * URL for manage orders page
     */
    const MCA = 'sales/order';

    /**
     * Navigation Menu Block
     *
     * @var string
     */
    protected $navigationMenuBlock = 'nav';

    /**
     * Sales order grid
     *
     * @var string
     */
    protected $gridBlock = '#sales_order_grid';

    /**
     * Messages block
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Order actions block
     *
     * @var string
     */
    protected $orderActionsBlock = '.page-actions';

    /**
     * Grid page actions block
     *
     * @var string
     */
    protected $gridPageActionsBlock = '.page-main-actions';

    /**
     * Order view tabs block
     *
     * @var string
     */
    protected $formTabsBlock = '#sales_order_view_tabs';

    /**
     * Transactions grid
     *
     * @var string
     */
    protected $transctionGrid = '#order_transactions';

    /**
     * Order returns block
     *
     * @var string
     */
    protected $orderReturnsBlock = 'order_rma';

    /**
     * Credit Memos grid
     *
     * @var string
     */
    protected $creditMemosGrid = '#order_creditmemos';

    /**
     * Get sales order grid
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Grid
     */
    public function getOrderGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderGrid(
            $this->_browser->find($this->gridBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get order actions block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Actions
     */
    public function getOrderActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderActions(
            $this->_browser->find($this->orderActionsBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get Order view tabs block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\View\OrderForm
     */
    public function getFormTabsBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderViewOrderForm(
            $this->_browser->find($this->formTabsBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get invoices grid
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Grid
     */
    public function getInvoicesGrid()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderInvoiceGrid(
            $this->_browser->find('#order_invoices')
        );
    }

    /**
     * Get transactions grid
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Transactions\Grid
     */
    public function getTransactionsGrid()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderTransactionsGrid(
            $this->_browser->find($this->transctionGrid, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get navigation menu items
     *
     * @return \Magento\Theme\Test\Block\Html\Topmenu
     */
    public function getNavigationMenuBlock()
    {
        return Factory::getBlockFactory()->getMagentoThemeHtmlTopmenu(
            $this->_browser->find($this->navigationMenuBlock, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get order returns block
     *
     * @return \Magento\Rma\Test\Block\Adminhtml\Order\View\Tab\Rma
     */
    public function getOrderReturnsBlock()
    {
        return Factory::getBlockFactory()->getMagentoRmaAdminhtmlOrderViewTabRma(
            $this->_browser->find($this->orderReturnsBlock, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get credit memos grid
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Creditmemo\Grid
     */
    public function getCreditMemosGrid()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreditmemoGrid(
            $this->_browser->find($this->creditMemosGrid)
        );
    }

    /**
     * Get grid page actions
     *
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return Factory::getBlockFactory()->getMagentoBackendGridPageActions(
            $this->_browser->find($this->gridPageActionsBlock)
        );
    }
}
