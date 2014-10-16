<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Magento\Sales\Test\Block\Adminhtml\Order\Create\Items;

/**
 * Class AssertGroupedProductInItemsOrderedGrid
 * Assert grouped product was added to Items Ordered grid in customer account on Order creation page backend
 */
class AssertGroupedProductInItemsOrderedGrid extends AbstractConstraint
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
     * @throws \Exception
     * @return void
     */
    public function processAssert(OrderCreateIndex $orderCreateIndex, array $entityData)
    {
        if (!isset($entityData['products'])) {
            throw new \Exception("No products");
        }
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
            $products = $product->getAssociated()['products'];
            foreach ($products as $key => $value) {
                $fixtureData[$key]['name'] = $value->getName();
                $fixtureData[$key]['price'] = $value->getPrice();
                $pageData[$key] = $itemsBlock->getItemProductByName($value->getName())->getCheckoutData($this->fields);
            }
            $options = $product->getCheckoutData()['options'];
            foreach ($options as $key => $option) {
                $fixtureData[$key]['checkout_data']['qty'] = $option['qty'];
            }
        }

        return ['fixtureData' => $fixtureData, 'pageData' => $pageData];
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
