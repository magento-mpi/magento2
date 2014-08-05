<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block;

use Mtf\Block\Block;

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
     * Widgets selectors
     *
     * @var array
     */
    protected $widgetSelectors = [
        'CMS Page Link' => '.widget.widget-cms-link',
        'Catalog Category Link' => '.widget.category.link',
        'Catalog Product Link' => '.widget.product.link',
        'Recently Compared Products' => '.block.compare',
        'Recently Viewed Products' => '.block.viewed.links',
        'Catalog New Products List' => '.widget.new',
        'CMS Static Block' => '.widget.static.block'
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
            return $this->_rootElement->find($this->widgetSelectors[$widgetType])->isVisible();
        } else {
            throw new \Exception('Determine how to find the widget on the page.');
        }
    }
}
