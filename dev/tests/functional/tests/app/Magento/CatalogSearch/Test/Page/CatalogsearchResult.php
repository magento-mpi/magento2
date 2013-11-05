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

namespace Magento\CatalogSearch\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Block\Product\ListProduct;

/**
 * Class CatalogsearchResult
 * Search result page
 *
 * @package Magento\CatalogSearch\Test\Page
 */
class CatalogsearchResult extends Page
{
    /**
     * URL for home page
     */
    const MCA = 'catalogsearch/result';

    /**
     * List of results of product search
     *
     * @var ListProduct
     */
    private $listProductBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->listProductBlock = Factory::getBlockFactory()->getMagentoCatalogProductListProduct(
            $this->_browser->find('.search.results', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get search results list block
     *
     * @return \Magento\Catalog\Test\Block\Product\ListProduct
     */
    public function getListProductBlock()
    {
        return $this->listProductBlock;
    }
}
