<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Category;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Tree
 * Categories tree block
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Category
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
     */
    public function addSubcategory()
    {
        $this->_rootElement->find($this->addSubcategory, Locator::SELECTOR_ID)->click();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Select Default category
     *
     * @param string $path
     */
    public function selectCategory($path)
    {
        $this->expandAllCategories();
        $this->_rootElement->find($this->treeElement, Locator::SELECTOR_CSS, 'tree')->setValue($path);
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Expand all categories tree
     */
    protected function expandAllCategories()
    {
        $this->_rootElement->find($this->expandAll)->click();
    }
}
