<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\Search;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Sales\Test\Fixture\Order;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Class Grid
 * Adminhtml sales order create search products block
 *
 */
class Grid extends GridInterface
{
    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-in_products';

    /**
     * Product 'Configure' button
     *
     * @var string
     */
    protected $configure = '.action-configure';

    /**
     * Select product checkbox
     *
     * @var string
     */
    protected $selectProduct = 'td.col-in_products input';

    /**
     * 'Add Selected Product(s) to Order' button
     *
     * @var string
     */
    protected $addSelectedProducts = '.actions button';

    /**
     * Catalog product composite configure block
     *
     * @var string
     */
    protected $configureBlock = '//span[text()="Configure Product"]//ancestor::div[@role="dialog"]';

    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'sku' => array(
            'selector' => '#sales_order_create_search_grid_filter_sku'
        )
    );

    /**
     * Get catalog product composite configure block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Composite\Configure
     */
    protected function getConfigureBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductCompositeConfigure(
            $this->_rootElement->find($this->configureBlock, Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Select product to be added to order
     *
     * @param Product $product
     */
    protected function addProduct($product)
    {
        $this->search(array(
            'sku' => $product->getProductSku()
        ));
        $productOptions = $product->getProductOptions();
        if (!empty($productOptions)) {
            $this->_rootElement->find($this->configure)->click();
            $this->getTemplateBlock()->waitLoader();
            $this->getConfigureBlock()->fillOptions($productOptions);
        }
        $this->_rootElement
            ->find($this->rowItem)
            ->find($this->selectProduct, Locator::SELECTOR_CSS, 'checkbox')
            ->setValue('Yes');
    }

    /**
     * Add all products from the Order fixture
     *
     * @param Order $fixture
     */
    public function selectProducts(Order $fixture)
    {
        foreach ($fixture->getProducts() as $product) {
            $this->addProduct($product);
        }
        $this->_rootElement->find($this->addSelectedProducts)->click();
        $this->getTemplateBlock()->waitLoader();
    }
}
