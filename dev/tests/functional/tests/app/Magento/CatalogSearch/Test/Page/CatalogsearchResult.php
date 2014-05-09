<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class CatalogsearchResult
 * Search result page
 *
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
     * @var string
     */
    protected $listProductBlock = '.search.results';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get search results list block
     *
     * @return \Magento\Catalog\Test\Block\Product\ListProduct
     */
    public function getListProductBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductListProduct(
            $this->_browser->find($this->listProductBlock, Locator::SELECTOR_CSS)
        );
    }
}
