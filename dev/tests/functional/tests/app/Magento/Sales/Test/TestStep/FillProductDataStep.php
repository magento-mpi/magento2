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
 * Class FillProductDataStep
 * Fill Product Data
 */
class FillProductDataStep implements TestStepInterface
{
    /**
     * Sales order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Products list
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
     * Fill product data
     *
     * @return void
     */
    public function run()
    {
        $this->orderCreateIndex->getCreateBlock()->updateProductData($this->products);
    }
}
