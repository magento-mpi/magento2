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
    protected $widgetSelectorsCss = [
        'Banner Rotator' => '.widget.banners',
        'CMS Page Link' => '.widget.widget-cms-link',
        'Catalog Category Link' => '.widget.category.link',
        'Catalog Product Link' => '.widget.product.link',
        'Recently Compared Products' => '.block.compare',
        'Recently Viewed Products' => '.block.viewed.links',
        'Catalog New Products List' => '.widget.new',
        'CMS Static Block' => '.widget.static.block'
    ];

    protected $widgetSelectors = [
        'Banner Rotator' => './/*/div[contains(@class,"widget banners") and contains(.,"%s")]',
        'CMS Page Link' => './/*/a[contains(.,"%s")]',
        'Catalog Category Link' => './/*/a[contains(.,"%s")]',
        'Catalog Product Link' => './/*/a[contains(.,"%s")]',
        'Recently Compared Products' => './/*/div[contains(@class,"block compare") and contains(.,"%s")]',
        'Recently Viewed Products' => './/*/div[contains(@class,"block viewed links") and contains(.,"%s")]',
        'Catalog New Products List' => './/*/div[contains(@class,"widget new") and contains(.,"%s")]',
        'CMS Static Block' => './/*/div[contains(@class,"widget static block") and contains(.,"%s")]'
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
     * @return bool
     * @throws \Exception
     */
    public function isWidgetVisible($widgetType)
    {
        if (isset($this->widgetSelectors[$widgetType])) {
            return $this->_rootElement->find($this->widgetSelectorsCss[$widgetType])->isVisible();
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
