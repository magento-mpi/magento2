<?php
/**
 * Store grid
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Test\Block\System\Store;

use Mtf\Client\Element\Locator;

class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Check if store exists
     *
     * @param string $title
     * @return bool
     */
    public function isStoreExists($title)
    {
        $element = $this->_rootElement->find($title, Locator::SELECTOR_LINK_TEXT);
        return $element && $element->isVisible();
    }
}
