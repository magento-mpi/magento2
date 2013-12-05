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
use Mtf\Client\Element\Locator;

/**
 * Class CatalogProductIndex
 * Catalog manage products grid page
 *
 * @package Magento\Catalog\Test\Page\Product
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
     * @var string
     */
    protected $backendProductGrid = '#productGrid';

    /**
     * Global page messages block
     *
     * @var string
     */
    protected $messageBlock = '#messages';

    /**
     * Add product block
     *
     * @var string
     */
    protected $productBlock = '#add_new_product';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get the backend catalog product block
     *
     * @return \Magento\Catalog\Test\Block\Backend\ProductGrid
     */
    public function getProductGrid()
    {
        return Factory::getBlockFactory()->getMagentoCatalogBackendProductGrid(
            $this->_browser->find($this->backendProductGrid, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get page messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messageBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get add product block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product
     */
    public function getProductBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProduct(
            $this->_browser->find($this->productBlock, Locator::SELECTOR_CSS)
        );
    }
}
