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
use Magento\Checkout\Test\Block\Multishipping;

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
     * Multishipping checkout success block
     *
     * @var Multishipping\Success
     */
    private $successBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;

        $this->successBlock = Factory::getBlockFactory()->getMagentoCheckoutMultishippingSuccess(
            $this->_browser->find('.multicheckout.success', Locator::SELECTOR_CSS));
    }

    /**
     * Get success block
     *
     * @return \Magento\Checkout\Test\Block\Multishipping\Success
     */
    public function getSuccessBlock()
    {
        return $this->successBlock;
    }
}
