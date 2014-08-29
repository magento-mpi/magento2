<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Test\Block;

use Magento\Banner\Test\Fixture\BannerInjectable;
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
     * Check Widget Banners
     *
     * @param BannerInjectable $banner
     * @return bool
     */
    public function checkWidgetBanners(BannerInjectable $banner)
    {
        return $this->_rootElement
            ->find(sprintf($this->widgetBanner, $banner->getStoreContents()['value_0']), Locator::SELECTOR_XPATH)
            ->isVisible();
    }
}
