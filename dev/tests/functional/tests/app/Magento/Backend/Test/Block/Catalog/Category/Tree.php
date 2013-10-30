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

namespace Magento\Backend\Test\Block\Catalog\Category;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Client\Driver\Selenium\Element;

/**
 * Class Tree
 * Categories tree block
 *
 * @package Magento\Backend\Test\Block\Catalog\Category
 */
class Tree extends Block
{
    /**
     * 'Add Subcategory' button
     *
     * @var string
     */
    private $addSubcategory;

    /**
     * Backend abstract block
     *
     * @var \Magento\Backend\Test\Block\Template
     */
    private $templateBlock;

    /**
     * Custom constructor
     *
     * @constructor
     * @param Element $element
     * @param $templateBlock
     */
    public function __construct(Element $element, $templateBlock)
    {
        parent::__construct($element);
        $this->templateBlock = $templateBlock;
    }

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->addSubcategory = 'add_subcategory_button';
    }

    /**
     * Press 'Add Subcategory' button
     */
    public function addSubcategory()
    {
        $this->_rootElement->find($this->addSubcategory, Locator::SELECTOR_ID)->click();
        $this->templateBlock->waitLoader();
    }

    /**
     * Select Default category
     *
     * @param string $path
     */
    public function selectCategory($path)
    {
        $this->expandAllCategories();
        $this->_rootElement->clickByPath($path);
        $this->templateBlock->waitLoader();
    }

    /**
     * Expand all categories tree
     */
    protected function expandAllCategories()
    {
        $this->_rootElement->find('.tree-actions > a:nth-of-type(2)')->click();
    }
}
