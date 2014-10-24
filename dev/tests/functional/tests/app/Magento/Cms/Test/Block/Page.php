<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Page
 * Cms Page block for the content on the frontend.
 */
class Page extends Block
{
    /**
     * Cms page content class
     *
     * @var string
     */
    protected $cmsPageContentClass = ".column.main";

    /**
     * Cms page title
     *
     * @var string
     */
    protected $cmsPageTitle = ".page-title";

    /**
     * Widgets selectors
     *
     * @var array
     */
    protected $widgetSelectors = [
        'Banner Rotator' => './/div[contains(@class,"widget banners") and contains(.,"%s")]',
        'CMS Page Link' => './/*/a[contains(.,"%s")]',
        'Catalog Category Link' => './/*/a[contains(.,"%s")]',
        'Catalog Product Link' => './/*/a[contains(.,"%s")]',
        'Recently Compared Products' => './/*/div[contains(@class,"block widget compared grid") and contains(.,"%s")]',
        'Recently Viewed Products' => './/*/div[contains(@class,"block widget viewed grid") and contains(.,"%s")]',
        'Catalog New Products List' => './/*/div[contains(@class,"widget new") and contains(.,"%s")]',
        'CMS Static Block' => './/*/div[contains(@class,"widget static block") and contains(.,"%s")]',
        'CMS Hierarchy Node Link' => './/*/a[contains(.,"%s")]',
        'Catalog Events Carousel' => '(//div[contains(@class,"widget")]//a/span[contains(.,"%s")])[last()]'
    ];

    /**
     * Get page content text
     *
     * @return string
     */
    public function getPageContent()
    {
        return $this->_rootElement->find($this->cmsPageContentClass)->getText();
    }

    /**
     * Check is visible widget selector
     *
     * @param string $widgetType
     * @param string $widgetText
     * @return bool
     * @throws \Exception
     */
    public function isWidgetVisible($widgetType, $widgetText)
    {
        if (isset($this->widgetSelectors[$widgetType])) {
            return $this->_rootElement->find(
                sprintf($this->widgetSelectors[$widgetType], $widgetText),
                Locator::SELECTOR_XPATH
            )->isVisible();
        } else {
            throw new \Exception('Determine how to find the widget on the page.');
        }
    }

    /**
     * Check is visible widget selector
     *
     * @return string
     * @throws \Exception
     */
    public function getPageTitle()
    {
        return $this->_rootElement->find($this->cmsPageTitle)->getText();
    }

    /**
     * Click to widget selector
     *
     * @param string $widgetType
     * @param string $widgetText
     * @return bool
     * @throws \Exception
     */
    public function clickToWidget($widgetType, $widgetText)
    {
        if (isset($this->widgetSelectors[$widgetType])) {
            $this->_rootElement->find(
                sprintf($this->widgetSelectors[$widgetType], $widgetText),
                Locator::SELECTOR_XPATH
            )->click();
        } else {
            throw new \Exception('Determine how to find the widget on the page.');
        }
    }
}
