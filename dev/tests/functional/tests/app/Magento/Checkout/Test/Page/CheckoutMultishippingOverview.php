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

namespace Magento\Checkout\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class CheckoutMultishippingOverview
 * Review order page
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutMultishippingOverview extends Page
{
    /**
     * URL for order overview page
     */
    const MCA = 'checkout/multishipping/overview';

    /**
     * Multishipping checkout overview block
     *
     * @var string
     */
    protected $overviewBlock = '#review-order-form';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get overview block
     *
     * @return \Magento\Checkout\Test\Block\Multishipping\Overview
     */
    public function getOverviewBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutMultishippingOverview(
            $this->_browser->find($this->overviewBlock, Locator::SELECTOR_CSS)
        );
    }
}
