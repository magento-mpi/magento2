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
 * Class SalesOrderView
 * Order view page
 *
 * @package Magento\Sales\Test\Page
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
     * Order information block
     *
     * @var string
     */
    protected $customerInformationBlock = '.order-account-information';

    /**
     * Page title block
     *
     * @var string
     */
    protected $titleBlock = '.page-title .title';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;
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
     * Get order totals block
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
     * @return \Magento\Sales\Test\Block\Backend\Order\CustomerInformation
     */
    public function getOrderCustomerInformationBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesBackendOrderCustomerInformation(
            $this->_browser->find($this->customerInformationBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get page title block
     *
     * @return \Magento\Page\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return Factory::getBlockFactory()->getMagentoPageHtmlTitle(
            $this->_browser->find($this->titleBlock, Locator::SELECTOR_CSS)
        );
    }
}
