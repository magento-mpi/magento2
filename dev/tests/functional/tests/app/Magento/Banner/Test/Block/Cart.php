<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Test\Block;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Client\Element\Locator;

/**
 * Class Cart
 * Shopping cart block
 */
class Cart extends \Magento\Checkout\Test\Block\Cart
{
    /**
     * Widget Banner CSS selector
     *
     * @var string
     */
    protected $widgetBanner = '//div[contains(@class, "widget banners")]/ul/li[text()="%s"]';

    /**
     * Header welcome message selector.
     *
     * @var string
     */
    protected $welcome = '.welcome';

    /**
     * Check Widget Banners.
     *
     * @param BannerInjectable $banner
     * @param CustomerInjectable|null $customer
     * @return bool
     */
    public function checkWidgetBanners(BannerInjectable $banner, CustomerInjectable $customer = null)
    {
        if ($customer !== null) {
            $browser = $this->browser;
            $welcome = $this->welcome;
            $browser->waitUntil(
                function () use ($browser, $welcome, $customer) {
                    $text = $browser->find($welcome)->getText();
                    return strpos($text, $customer->getFirstname()) ? true : null;
                }
            );
        }

        return $this->_rootElement
            ->find(sprintf($this->widgetBanner, $banner->getStoreContents()['value_0']), Locator::SELECTOR_XPATH)
            ->isVisible();
    }
}
