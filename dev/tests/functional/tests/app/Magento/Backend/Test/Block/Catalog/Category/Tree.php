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
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

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
     * Default category selector
     *
     * @var string
     */
    private $defaultCategory;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->addSubcategory = 'add_subcategory_button';
        $this->defaultCategory = '#extdd-2';
    }

    /**
     * Press 'Add Subcategory' button
     */
    public function addSubcategory()
    {
        $this->_rootElement->find($this->addSubcategory, Locator::SELECTOR_ID)->click();
    }

    /**
     * Select Default category
     */
    public function selectDefaultCategory()
    {
        $this->_rootElement->find($this->defaultCategory, Locator::SELECTOR_CSS)->click();
    }
}
