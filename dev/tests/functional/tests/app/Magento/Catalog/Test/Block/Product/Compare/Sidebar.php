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
     * Product name selector
     *
     * @var string
     */
    protected $productName = 'li.item strong.name a';

    /**
     * Get compare products block content
     *
     * @return array|string
     */
    public function getProducts()
    {
        $result = [];
        $isEmpty = $this->_rootElement->find($this->isEmpty);
        if ($isEmpty->isVisible()) {
            return $isEmpty->getText();
        }
        $elements = $this->_rootElement->find($this->productName)->getElements();
        foreach ($elements as $element) {
            $result[] = $element->getText();
        }
        return $result;
    }
}
