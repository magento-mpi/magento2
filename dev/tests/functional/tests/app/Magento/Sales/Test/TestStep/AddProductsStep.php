<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;

/**
 * Class AddProductsStep
 * Add Product Step
 */
class AddProductsStep implements TestStepInterface
{
    /**
     * Sales order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Catalog Product Simple
     *
     * @var array
     */
    protected $products;

    /**
     * @constructor
     * @param OrderCreateIndex $orderCreateIndex
     * @param array $products
     */
    public function __construct(OrderCreateIndex $orderCreateIndex, array $products)
    {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->products = $products;
    }

    /**
     * Add product to sales
     *
     * @return void
     */
    public function run()
    {
        $createBlock = $this->orderCreateIndex->getCreateBlock();
        $createBlock->getItemsBlock()->clickAddProducts();
        foreach ($this->products as $product) {
            $createBlock->getGridBlock()->searchAndSelect(['sku' => $product->getSku()]);
            $createBlock->getTemplateBlock()->waitLoader();
            if ($this->orderCreateIndex->getConfigureProductBlock()->isVisible()) {
                $this->orderCreateIndex->getConfigureProductBlock()->configProduct($product);
            }
        }
        $createBlock->addSelectedProductsToOrder();
        $createBlock->getTemplateBlock()->waitLoader();
    }
}
