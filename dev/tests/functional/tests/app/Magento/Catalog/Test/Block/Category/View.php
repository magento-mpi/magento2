<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Category;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class View
 * Category view block on the category page
 */
class View extends Block
{
    /**
     * Description CSS selector
     *
     * @var string
     */
    protected $description = '.category-description';

    /**
     * Widget Banner CSS selector
     *
     * @var string
     */
    protected $widgetBanner = './/*/li[text()="%s"]';

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_rootElement->find($this->description)->getText();
    }

    /**
     * Get Category Content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_rootElement->getText();
    }

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
