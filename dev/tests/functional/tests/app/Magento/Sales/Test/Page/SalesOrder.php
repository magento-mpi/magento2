<?php
/**
 * {license_notice}
 *
 * @category Mtf
 * @package Mtf
 * @subpackage functional_tests
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Sales\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Magento\Backend\Test\Block\Sales\Order\Grid;
use Magento\Core\Test\Block\Messages;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Backend\Test\Block\Sales\Order\Transactions\Grid as TransactionsGrid;
use Magento\Backend\Test\Block\Sales\Order\Invoice\Grid as InvoiceGrid;
use Mtf\Client\Element\Locator;

/**
 * Class SalesOrder
 * Manage orders page
 *
 * @package Magento\Sales\Test\Page
 */
class SalesOrder extends Page
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
    protected $messageBlock = '#messages .messages';

    /**
     * Order actions block
     *
     * @var string
     */
    protected $orderActionsBlock = '.page-actions';

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
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

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
            $this->_browser->find($this->messageBlock, Locator::SELECTOR_CSS)
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
     * @return \Magento\Backend\Test\Block\Widget\FormTabs
     */
    public function getFormTabsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendWidgetFormTabs(
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
}
