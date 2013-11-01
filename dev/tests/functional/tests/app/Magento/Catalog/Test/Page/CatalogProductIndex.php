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

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Core\Test\Block\Messages;
use Magento\Backend\Test\Block\Catalog\Product;
use Magento\Catalog\Test\Block\Backend\ProductGrid;

/**
 * Class CatalogProductIndex
 * Catalog manage products grid page
 *
 * @package Magento\Catalog\Test\Page
 */
class CatalogProductIndex extends Page
{
    /**
     * URL for catalog product grid
     */
    const MCA = 'catalog/product/index';

    /**
     * Catalog product grid on backend
     *
     * @var ProductGrid
     */
    private $backendProductGrid;

    /**
     * Global page messages block
     *
     * @var Messages
     */
    private $messageBlock;

    /**
     * Add product block
     *
     * @var Product
     */
    private $productBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->backendProductGrid = Factory::getBlockFactory()->getMagentoCatalogBackendProductGrid(
            $this->_browser->find('productGrid', Locator::SELECTOR_ID)
        );
        $this->messageBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('messages', Locator::SELECTOR_ID)
        );
        $this->productBlock = Factory::getBlockFactory()->getMagentoBackendCatalogProduct(
            $this->_browser->find('add_new_product', Locator::SELECTOR_ID)
        );
    }

    /**
     * Get the backend catalog product block
     *
     * @return ProductGrid
     */
    public function getProductGrid()
    {
        return $this->backendProductGrid;
    }

    /**
     * Get page messages block
     *
     * @return Messages
     */
    public function getMessageBlock()
    {
        return $this->messageBlock;
    }

    /**
     * Get add product block
     *
     * @return Product
     */
    public function getProductBlock()
    {
        return $this->productBlock;
    }
}
