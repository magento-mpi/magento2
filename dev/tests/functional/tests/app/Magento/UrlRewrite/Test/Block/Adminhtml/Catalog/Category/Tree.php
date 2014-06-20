<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Category;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Tree
 * Categories tree block
 */
class Tree extends Block
{
    /**
     * Locator value for  skip category button
     *
     * @var string
     */
    protected $skipCategoryButton = '[data-ui-id="urlrewrite-catalog-product-edit-skip-categories"]';

    /**
     * Select category by its name
     *
     * @param string $categoryName
     * @return void
     */
    public function selectCategory($categoryName)
    {
        $this->_rootElement->find("//a[contains(text(),'{$categoryName}')]", Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Skip category selection
     *
     * @return void
     */
    public function skipCategorySelection()
    {
        $this->_rootElement->find($this->skipCategoryButton, Locator::SELECTOR_CSS)->click();
    }
}
