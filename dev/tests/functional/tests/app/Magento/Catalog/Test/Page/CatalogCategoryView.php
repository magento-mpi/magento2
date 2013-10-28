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

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class CatalogCategoryView
 * Create product page
 *
 * @package Magento\Catalog\Test\Page\CatalogCategoryView
 */
class CatalogCategoryView extends Page
{
    /**
     * URL for catalog product grid
     */
    const MCA = 'catalog/category/view';

    /**
     * List of results of product search
     *
     * @var \Magento\Catalog\Test\Block\Product\ListProduct
     */
    private $listProductBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'];
        $this->listProductBlock = Factory::getBlockFactory()->getMagentoCatalogProductListProduct(
            $this->_browser->find('.products.wrapper.grid', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Open category by name from menu
     *
     * @param string $categoryName
     */
    public function openCategory($categoryName)
    {
        $this->open();
        $location = '//nav[@class="navigation"]//a[span[text()="' . $categoryName . '"]]';
        $this->_browser->find($location, Locator::SELECTOR_XPATH)->click();
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
}
