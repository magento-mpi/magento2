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

namespace Magento\Sales\Test\Block\Backend\Order;

use Magento\Backend\Test\Block\Widget\Grid;
use Magento\Catalog\Test\Fixture\SimpleProduct;
use Magento\Sales\Test\Fixture\Order;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Grid for adding products for order in backend
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class ProductsAddGrid extends Grid
{
    /**
     * @var ConfigureProduct
     */
    protected $configureProductBlock;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->selectItem = 'tbody tr .col-in_products';
        $this->configureProductBlock = Factory::getBlockFactory()
            ->getMagentoSalesBackendOrderConfigureProduct(
                $this->_rootElement->find(
                    '//span[text()="Configure Product"]//ancestor::div[@role="dialog"]',
                    Locator::SELECTOR_XPATH
                )
            );
        $this->filters = array(
            'sku' => array(
                'selector' => '#sales_order_create_search_grid_filter_sku'
            ),
        );
    }

    /**
     * Add selected products to order
     */
    public function addSelectedProducts()
    {
        $this->_rootElement->find('.actions button')->click();
        $this->templateBlock->waitLoader();
    }

    /**
     * Select product to be added to order
     *
     * @param SimpleProduct $product
     */
    public function addProduct($product)
    {
        $this->search(array(
            'sku' => $product->getProductSku()
        ));
        $productOptions = $product->getProductOptions();
        if (!empty($productOptions)) {
            $this->_rootElement->find('.action-configure')->click();
            $this->templateBlock->waitLoader();
            $this->configureProductBlock->fillOptions($productOptions);
        }
        $this->_rootElement
            ->find($this->rowItem)
            ->find('td.col-in_products input', Locator::SELECTOR_CSS, 'checkbox')
            ->setValue('Yes');
    }

    /**
     * Add all products from the Order fixture
     *
     * @param Order $fixture
     */
    public function addProducts(Order $fixture)
    {
        foreach ($fixture->getProducts() as $product) {
            $this->addProduct($product);
        }
        $this->addSelectedProducts();
        $this->templateBlock->waitLoader();
    }
}
