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
use Magento\Checkout\Test\Block\Onepage;

/**
 * Class CheckoutOnepageSuccess
 * One page checkout success page
 *
 * @package Magento\Checkout\Test\Page
 */
class CheckoutOnepageSuccess extends Page
{
    /**
     * URL for checkout success page
     */
    const MCA = 'checkout/onepage/success';

    /**
     * One page checkout success block
     *
     * @var Onepage\Success
     */
    private $successBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;

        $this->successBlock = Factory::getBlockFactory()->getMagentoCheckoutOnepageSuccess(
            $this->_browser->find('.col-main', Locator::SELECTOR_CSS));
    }

    /**
     * Get one page success block
     *
     * @return \Magento\Checkout\Test\Block\Onepage\Success
     */
    public function getSuccessBlock()
    {
        return $this->successBlock;
    }
}
