<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Widget block on the frontend
 */
class WidgetView extends Block
{
    /**
     * Widgets selectors
     *
     * @var array
     */
    protected $widgetSelectors = [
        'cmsPageLink' => '/descendant-or-self::div//a[contains(.,"%s")]',
        'catalogCategoryLink' => './/*/a[contains(.,"%s")]',
        'catalogProductLink' => './/*/a[contains(.,"%s")]',
        'recentlyComparedProducts' => '/descendant-or-self::div[contains(.,"%s")]',
        'recentlyViewedProducts' => '/descendant-or-self::div[contains(.,"%s")]',
        'cmsStaticBlock' => '/descendant-or-self::div[contains(.,"%s")]'
    ];

    /**
     * Check is visible widget selector
     *
     * @param Widget $widget
     * @param string $widgetText
     * @return bool
     * @throws \Exception
     */
    public function isWidgetVisible(Widget $widget, $widgetText)
    {
        $widgetType = $widget->getWidgetOptions()[0]['type_id'];
        if ($this->hasRender($widgetType)) {
            return $this->callRender(
                $widgetType,
                'isWidgetVisible',
                ['widget' => $widget, 'widgetText' => $widgetText]
            );
        } else {
            if (isset($this->widgetSelectors[$widgetType])) {
                return $this->_rootElement->find(
                    sprintf($this->widgetSelectors[$widgetType], $widgetText),
                    Locator::SELECTOR_XPATH
                )->isVisible();
            } else {
                throw new \Exception('Determine how to find the widget on the page.');
            }
        }
    }

    /**
     * Click to widget selector
     *
     * @param Widget $widget
     * @param string $widgetText
     * @return void
     * @throws \Exception
     */
    public function clickToWidget(Widget $widget, $widgetText)
    {
        $widgetType = $widget->getWidgetOptions()[0]['type_id'];
        if ($this->hasRender($widgetType)) {
            $this->callRender($widgetType, 'clickToWidget', ['widget' => $widget, 'widgetText' => $widgetText]);
        } else {
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
}
