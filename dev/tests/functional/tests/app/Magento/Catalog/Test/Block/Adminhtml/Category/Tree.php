<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Category;

use Magento\Catalog\Test\Fixture\CatalogCategory;
use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Tree
 * Categories tree block
 */
class Tree extends Block
{
    /**
     * 'Add Subcategory' button
     *
     * @var string
     */
    protected $addSubcategory = 'add_subcategory_button';

    /**
     * 'Expand All' link
     *
     * @var string
     */
    protected $expandAll = 'a[onclick*=expandTree]';

    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Category tree
     *
     * @var string
     */
    protected $treeElement = '.tree-holder';

    /**
     * Get backend abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    protected function getTemplateBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Press 'Add Subcategory' button
     *
     * @return void
     */
    public function addSubcategory()
    {
        $this->_rootElement->find($this->addSubcategory, Locator::SELECTOR_ID)->click();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Select Default category
     *
     * @param CatalogCategory $category
     * @return void
     */
    public function selectCategory(CatalogCategory $category)
    {
        $parentPath = $this->prepareFullCategoryPath($category);
        $path = implode('/', $parentPath);
        $this->expandAllCategories();
        $this->_rootElement->find($this->treeElement, Locator::SELECTOR_CSS, 'tree')->setValue($path);
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Prepare category path
     *
     * @param CatalogCategory $category
     * @return array
     */
    protected function prepareFullCategoryPath(CatalogCategory $category)
    {
        $path = [];
        if ($category->getDataFieldConfig('parent_id')['source']->getParentCategory() != null) {
            $path = $this->prepareFullCategoryPath(
                $category->getDataFieldConfig('parent_id')['source']->getParentCategory()
            );
        }
        return array_filter(array_merge($path, [$category->getPath(), $category->getName()]));
    }

    /**
     * Find category name in array
     *
     * @param array $structure
     * @param array $category
     * @return bool
     */
    protected function inTree(array $structure, array &$category)
    {
        $element = array_shift($category);
        foreach ($structure as $item) {
            $result = strpos($item['name'], $element);
            if ($result !== false && !empty($item['subnodes'])) {
                return $this->inTree($item['subnodes'], $category);
            } elseif ($result !== false && empty($category)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check category in category tree
     *
     * @param $category
     * @return bool
     */
    public function isCategoryVisible($category)
    {
        $categoryPath = $this->prepareFullCategoryPath($category);
        $structure = $this->_rootElement->find($this->treeElement, Locator::SELECTOR_CSS, 'tree')->getStructure();
        $result = false;
        $element = array_shift($categoryPath);
        foreach ($structure as $item) {
            $searchResult = strpos($item['name'], $element);
            if ($searchResult !== false && !empty($item['subnodes'])) {
                $result = $this->inTree($item['subnodes'], $categoryPath);
            } elseif ($searchResult !== false && empty($categoryPath)) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Expand all categories tree
     *
     * @return void
     */
    protected function expandAllCategories()
    {
        $this->_rootElement->find($this->expandAll)->click();
    }
}
