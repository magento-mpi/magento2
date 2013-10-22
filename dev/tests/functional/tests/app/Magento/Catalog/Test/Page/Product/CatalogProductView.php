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

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Block\Product;

/**
 * Class CatalogProductView
 * Frontend product view page
 *
 * @package Magento\Catalog\Test\Page\Product
 */
class CatalogProductView extends Page
{
    /**
     * URL for catalog product grid
     */
    const MCA = 'catalog/product/view';

    /**
     * Product View block
     *
     * @var Product\View
     */
    private $viewBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->viewBlock = Factory::getBlockFactory()->getMagentoCatalogProductView(
            $this->_browser->find('.column.main', Locator::SELECTOR_CSS));
    }

    /**
     * Page initialization
     *
     * @param DataFixture $fixture
     */
    public function init(DataFixture $fixture)
    {
        $this->_url = $_ENV['app_frontend_url'] . $fixture->getProductUrl() . '.html';
    }

    /**
     * Get product view block
     *
     * @return \Magento\Catalog\Test\Block\Product\View
     */
    public function getViewBlock()
    {
        return $this->viewBlock;
    }
}
