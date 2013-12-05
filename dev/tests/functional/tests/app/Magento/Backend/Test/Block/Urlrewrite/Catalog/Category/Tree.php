<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Urlrewrite\Catalog\Category;

use Mtf\Block\Block,
    Mtf\Client\Element\Locator;

/**
 * Class Tree
 * Categories tree block
 *
 * @package Magento\Backend\Test\Block\Urlrewrite\Catalog\Category
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
