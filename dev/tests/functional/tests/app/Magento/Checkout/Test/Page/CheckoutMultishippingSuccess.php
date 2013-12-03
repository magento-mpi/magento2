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
 * Class CheckoutMultishippingSuccess
 * Multishipping checkout success page
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutMultishippingSuccess extends Page
{
    /**
     * URL for multishipping success page
     */
    const MCA = 'checkout/multishipping/success';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get success block
     *
     * @return \Magento\Checkout\Test\Block\Multishipping\Success
     */
    public function getSuccessBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutMultishippingSuccess(
            $this->_browser->find('.multicheckout.success', Locator::SELECTOR_CSS)
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
            $this->_browser->find('.page.title', Locator::SELECTOR_CSS)
        );
    }
}
