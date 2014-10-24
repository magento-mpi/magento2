<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Mtf\TestStep\TestStepInterface;

/**
 * Configure products.
 */
class ConfigureProductsStep implements TestStepInterface
{
    /**
     * Products fixtures.
     *
     * @var array
     */
    protected $products = [];

    /**
     * Order create index page.
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * @construct
     * @param array $products
     * @param OrderCreateIndex $orderCreateIndex
     */
    public function __construct(array $products, OrderCreateIndex $orderCreateIndex)
    {
        $this->products = $products;
        $this->orderCreateIndex = $orderCreateIndex;
    }

    /**
     * Add Recently Viewed Products to cart.
     *
     * @return void
     */
    public function run()
    {
        $orderPage = $this->orderCreateIndex;
        foreach ($this->products as $product) {
            $orderPage->getCreateBlock()->getItemsBlock()->getItemProductByName($product->getName())->configure();
            $orderPage->getConfigureProductBlock()->configProduct($product);
        }
        $orderPage->getCreateBlock()->updateItems();
    }
}
