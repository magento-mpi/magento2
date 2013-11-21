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

use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell;
use Magento\Catalog\Test\Block\Backend\ProductForm;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class CatalogProductEdit
 * Edit product page
 */
class CatalogProductEdit extends Page
{
    /**
     * URL for product creation
     */
    const MCA = 'catalog/product/edit';

    /*
     * Selector for message block
     *
     * @var string
     */
    protected $messagesSelector = '#messages.messages .messages';

    /**
     * Messages block
     *
     * @var \Magento\Core\Test\Block\Messages
     */
    protected $messagesBlock;

    /**
     * @var ProductForm
     */
    private $productFormBlock;

    /**
     * Messages block
     *
     * @var \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell
     */
    protected $productUpsellBlock;

    /**
     * @var \Magento\Catalog\Test\Block\Backend\ProductUpsellGrid
     */
    protected $productUpsellGrid;

    /**
     * Catalog product grid on backend
     *
     * @var \Magento\Catalog\Test\Block\Backend\ProductEditGrid
     */
    private $productEditGrid;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->messagesBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesSelector, Locator::SELECTOR_CSS)
        );

        $this->productFormBlock = Factory::getBlockFactory()->getMagentoCatalogBackendProductForm(
            $this->_browser->find('body', Locator::SELECTOR_CSS)
        );

        $this->productUpsellBlock = Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabUpsell(
            $this->_browser->find('product_info_tabs_upsell', Locator::SELECTOR_CSS)
        );

        $this->productUpsellGrid = Factory::getBlockFactory()->getMagentoCatalogBackendProductUpsellGrid(
            $this->_browser->find('up_sell_product_grid', Locator::SELECTOR_ID)
        );

        $this->productEditGrid = Factory::getBlockFactory()->getMagentoCatalogBackendProductEditGrid(
            $this->_browser->find('related_product_grid', Locator::SELECTOR_ID)
        );
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->messagesBlock;
    }

    /**
     * Get messages block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell
     */
    public function getUpsellBlock()
    {
        return $this->productUpsellBlock;
    }


    /**
     * Get product form block
     *
     * @return \Magento\Catalog\Test\Block\Backend\ProductForm
     */
    public function getProductBlockForm()
    {
        return $this->productFormBlock;
    }


    /**
     * Get the backend catalog product block for upsells
     *
     * @return \Magento\Catalog\Test\Block\Backend\ProductUpsellGrid
     */
    public function getProductUpsellGrid()
    {
        return $this->productUpsellGrid;
    }


    /**
     * Get the backend catalog product block
     *
     * @return \Magento\Catalog\Test\Block\Backend\ProductEditGrid
     */
    public function getProductEditGrid()
    {
        return $this->productEditGrid;
    }
}
