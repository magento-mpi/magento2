<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page;

use Magento\Catalog\Test\Block\CategoryTitle;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Mtf\Page\Page;

/**
 * Class Category
 * Create product page
 *
 * @package Magento\Catalog\Test\Page\Category
 */
class Category extends Page
{
    /**
     * URL for catalog product grid
     */
    const MCA = 'catalog/category/view';

    /**
     * List of results of product search
     *
     * @var ListProduct
     */
    private $listProductBlock;

    /**
     * @var CategoryTitle
     */
    private $categoryTitleBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->listProductBlock = Factory::getBlockFactory()->getMagentoCatalogProductListProduct(
            $this->_browser->find('.products wrapper grid', Locator::SELECTOR_CSS)
        );
        $this->categoryTitleBlock = Factory::getBlockFactory()->getMagentoCatalogCategoryTitle(
            $this->_browser->find('.category-title', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Page initialization
     *
     * @param DataFixture $fixture
     */
    public function init(DataFixture $fixture)
    {
        $this->_url = $this->_url . '/id/' . $fixture->getCategoryId();
    }

    /**
     * @param string $categoryId
     */
    public function initUrl($categoryId)
    {
        $this->_url = $this->_url . '/id/' . $categoryId;
    }
    /**
     * Get product list block
     *
     * @return \Magento\Catalog\Test\Block\Product\ListProduct
     */
    public function getListProductBlock()
    {
        return $this->listProductBlock;
    }

    /**
     * Get category title block
     *
     * @return CategoryTitle
     */
    public function getCategoryTitleBlock()
    {
        return $this->categoryTitleBlock;
    }
}