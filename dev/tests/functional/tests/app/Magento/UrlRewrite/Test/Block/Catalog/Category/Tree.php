<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Block\Catalog\Category;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Tree
 * Categories tree block
 *
 */
class Tree extends Block
{
    /**
     * Select category by its name
     *
     * @param string $categoryName
     */
    public function selectCategory($categoryName)
    {
        $this->_rootElement->find("//a[contains(text(),'{$categoryName}')]", Locator::SELECTOR_XPATH)->click();
    }
}
