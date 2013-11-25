<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
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
     * Get Order view tabs block
     *
     * @return \Magento\Backend\Test\Block\Widget\FormTabs
     */
    public function getTabsWidget()
    {
        return Factory::getBlockFactory()->getMagentoBackendWidgetFormTabs(
            $this->_browser->find('#sales_order_view_tabs')
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
            $this->_browser->find('#order_transactions')
        );
    }
}
