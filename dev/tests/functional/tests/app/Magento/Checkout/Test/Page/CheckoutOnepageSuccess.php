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
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get one page success block
     *
     * @return \Magento\Checkout\Test\Block\Onepage\Success
     */
    public function getSuccessBlock()
    {
        return Factory::getBlockFactory()->getMagentoCheckoutOnepageSuccess(
            $this->_browser->find('//div[contains(@class, "column main")]', Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Get page title block
     *
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return Factory::getBlockFactory()->getMagentoPageHtmlTitle(
            $this->_browser->find('.page.title', Locator::SELECTOR_CSS)
        );
    }
}
