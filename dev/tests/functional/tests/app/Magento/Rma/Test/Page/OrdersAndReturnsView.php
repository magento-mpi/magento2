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

namespace Magento\Rma\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class OrdersAndReturnsView
 * Manage orders and returns page
 *
 * @package Magento\Rma\Test\Page
 */
class OrdersAndReturnsView extends Page
{
    /**
     * URL for orders and returns view page
     */
    const MCA = 'sales/guest/view';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get OrdersAndReturnsView block
     *
     * @return \Magento\Rma\Test\Block\OrdersAndReturnsView
     */
    public function getOrdersAndReturnsViewBlock()
    {
        return Factory::getBlockFactory()->getMagentoRmaOrdersAndReturnsView(
            $this->_browser->find('.order.toolbar', Locator::SELECTOR_CSS)
        );
    }
}