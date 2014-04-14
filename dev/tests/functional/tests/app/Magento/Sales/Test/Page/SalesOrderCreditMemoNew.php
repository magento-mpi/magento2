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
 * Class SalesOrderCreditMemoNew
 * Manage credit memo page
 *
 * @package Magento\Sales\Test\Page
 */
class SalesOrderCreditMemoNew extends Page
{
    /**
     * URL for manage credit memo page
     */
    const MCA = 'sales/order_creditmemo/new';

    /**
     * Actions block
     *
     * @var string
     */
    protected $actionsBlock = '.order-totals-bottom .actions';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get credit memo actions block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Actions
     */
    public function getActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderActions(
            $this->_browser->find($this->actionsBlock, Locator::SELECTOR_CSS)
        );
    }
}
