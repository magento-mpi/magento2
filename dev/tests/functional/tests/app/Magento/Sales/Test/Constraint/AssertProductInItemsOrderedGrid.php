<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Magento\Sales\Test\Block\Adminhtml\Order\Create\Items;

/**
 * Class AssertProductInItemsOrderedGrid
 * Assert product was added to Items Ordered grid in customer account on Order creation page backend
 */
class AssertProductInItemsOrderedGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Fields for assert
     *
     * @var array
     */
    protected $fields = ['name' => '', 'price' => '', 'checkout_data' => ['qty' => '']];

    /**
     * Check configured products
     *
     * @var bool
     */
    protected $productsIsConfigured;

    /**
     * Assert product was added to Items Ordered grid in customer account on Order creation page backend
     *
     * @param OrderCreateIndex $orderCreateIndex
     * @param array $entityData
     * @param bool $productsIsConfigured
     * @throws \Exception
     * @return void
     */
    public function processAssert(OrderCreateIndex $orderCreateIndex, array $entityData, $productsIsConfigured = true)
    {
        if (!isset($entityData['products'])) {
            throw new \Exception("No products");
        }
        $this->productsIsConfigured = $productsIsConfigured;
        $data = $this->prepareData($entityData, $orderCreateIndex->getCreateBlock()->getItemsBlock());

        \PHPUnit_Framework_Assert::assertEquals(
            $data['fixtureData'],
            $data['pageData'],
            'Wrong duplicate message is displayed.'
        );
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @param Items $itemsBlock
     * @return array
     */
    protected function prepareData(array $data, Items $itemsBlock)
    {
        $fixtureData = [];
        $pageData = [];
        foreach ($data['products'] as $product) {
            $fixtureData[] = [
                'name' => $product->getName(),
                'price' => number_format($this->getProductPrice($product), 2),
                'checkout_data' => ['qty' => $this->productsIsConfigured ? $product->getCheckoutData()['qty'] : 1],
            ];
            $pageData[] = $itemsBlock->getItemProductByName($product->getName())->getCheckoutData($this->fields);
        }

        return ['fixtureData' => $fixtureData, 'pageData' => $pageData];
    }

    /**
     * Get product price
     *
     * @param FixtureInterface $product
     * @return int
     */
    protected function getProductPrice(FixtureInterface $product)
    {
        return $product->getPrice();
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is added to Items Ordered grid from "Last Ordered Items" section on Order creation page.';
    }
}
