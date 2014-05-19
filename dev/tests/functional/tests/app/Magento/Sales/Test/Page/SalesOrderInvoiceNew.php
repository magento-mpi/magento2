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

/**
 * Class SalesOrder
 * Manage orders page
 *
 */
class SalesOrderInvoiceNew extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'sales/order/invoice/new';

    /**
     * Invoice form block
     *
     * @var string
     */
    protected $formBlock = '#edit_form';

    /**
     * Invoice totals block
     *
     * @var string
     */
    protected $totalsBlock = '#invoice_totals';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get invoice totals block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Totals
     */
    public function getInvoiceTotalsBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderInvoiceTotals(
            $this->_browser->find($this->totalsBlock)
        );
    }

    /**
     * Get invoice form block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Form
     */
    public function getFormBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderInvoiceCreateForm(
            $this->_browser->find('#edit_form')
        );
    }
}
