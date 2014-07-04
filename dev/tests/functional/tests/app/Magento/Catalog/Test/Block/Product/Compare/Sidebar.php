<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\Compare;

use Mtf\Block\Block;
use Mtf\Client\Element;

/**
 * Class Sidebar
 * Compare product block on cms page
 */
class Sidebar extends Block
{
    /**
     * Selector for empty message
     *
     * @var string
     */
    protected $isEmpty = 'p.empty';

    /**
     * Get compare products block content
     *
     * @return array|string
     */
    public function getContent()
    {
        $isEmpty = $this->_rootElement->find($this->isEmpty);
        if ($isEmpty->isVisible()) {
            return $isEmpty->getText();
        }
        // TODO after fix bug MAGETWO-22756 add next steps and return array
    }
}
