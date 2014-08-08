<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Block\Category;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Catalog\Test\Block\Category\View as AbstractView;
use Mtf\Client\Element\Locator;

/**
 * Class View
 * Category view block on the category page
 */
class View extends AbstractView
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
