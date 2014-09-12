<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Category;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class View
 * Category view block on the category page
 */
class View extends Block
{
    /**
     * Widgets selectors
     *
     * @var array
     */
    protected $widgetSelectors = [
        'Banner Rotator' => './/div[contains(@class,"widget banners") and contains(text(),"%s")]',
        'CMS Page Link' => './/div[contains(@class,"widget widget-cms-link") and contains(text(),"%s")]',
        'Catalog Category Link' => './/*/a[contains(.,"%s")]',
        'Catalog Product Link' => './/*/a[contains(.,"%s")]',
        'Recently Compared Products' => './/div[contains(@class,"block compare") and contains(text(),"%s")]',
        'Recently Viewed Products' => './/div[contains(@class,"block viewed links") and contains(text(),"%s")]',
        'Catalog New Products List' => './/div[contains(@class,"widget new") and contains(text(),"%s")]',
        'CMS Static Block' => './/div[contains(@class,"widget static block") and contains(text(),"%s")]'
    ];

    /**
     * Description CSS selector
     *
     * @var string
     */
    protected $description = '.category-description';

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
