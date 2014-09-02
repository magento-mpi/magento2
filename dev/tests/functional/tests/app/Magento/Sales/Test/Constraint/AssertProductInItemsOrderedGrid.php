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
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

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
    protected $fields = ['name', 'price', 'qty'];

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
        foreach ($data['products'] as $key => $product) {
            $fixtureData[] = [
                'name' => $data['data'][$key]['name'],
                'price' => number_format($this->getProductPrice($product), 2),
                'qty' => $data['data'][$key]['qty'],
            ];
            $pageData[] = $itemsBlock->getItemProductByName($product->getName())->getData($this->fields);
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
        $price = $product->getPrice();
        preg_match('/CatalogProduct(.*)/', get_class($product), $matches);
        $methodName = 'get' . $matches[1] . 'Price';
        if (method_exists($this, $methodName)) {
            $price += $this->$methodName($product);
        }

        return $price;
    }

    /**
     * Get configurable product price
     *
     * @param FixtureInterface $product
     * @throws \Exception
     * @return int
     */
    protected function getConfigurablePrice(FixtureInterface $product)
    {
        $price = 0;
        if (!$product instanceof CatalogProductConfigurable) {
            throw new \Exception("Product '$product->getName()' is not configurable product.");
        }
        $checkoutData = $product->getCheckoutData();
        if ($checkoutData === null) {
            return 0;
        }
        $attributesData = $product->getConfigurableAttributesData()['attributes_data'];
        foreach ($checkoutData['configurable_options'] as $option) {
            $attributeIndex = str_replace('attribute_', '', $option['title']);
            $optionIndex = str_replace('option_', '', $option['value']);
            $itemOption = $attributesData[$attributeIndex]['options'][$optionIndex];
            $itemPrice = $itemOption['is_percent'] == 'No'
                ? $itemOption['pricing_value']
                : $product->getPrice() / 100 * $itemOption['pricing_value'];
            $price += $itemPrice;
        }

        return $price;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is added to Items Ordered grid in customer account on Order creation page backend.';
    }
}
