<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Block\Category;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Client\Element\Locator;

/**
 * Class View
 * Category view block on the category page
 */
class View extends \Magento\Catalog\Test\Block\Category\View
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
