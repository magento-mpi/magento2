<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Page\Product\CatalogProductCompare;

/**
 * Class AssertProductComparePage
 * Assert that "Compare Product" page contains product(s) that was added
 */
class AssertProductComparePage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Product attribute on compare product page
     *
     * @var array
     */
    protected $attributeProduct = [
        'name',
        'price',
        'sku' => 'SKU',
        'description' => 'Description',
        'short_description' => 'Short Description'
    ];

    /**
     * Assert that "Compare Product" page contains product(s) that was added
     * - Product name
     * - Price
     * - SKU
     * - Description (if exists, else text "No")
     * - Short Description (if exists, else text "No")
     *
     * @param array $products
     * @param CatalogProductCompare $comparePage
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(
        array $products,
        CatalogProductCompare $comparePage,
        CmsIndex $cmsIndex
    ) {
        $cmsIndex->open();
        $cmsIndex->getLinksBlock()->openLink("Compare Products");
        foreach ($products as $key => $product) {
            foreach ($this->attributeProduct as $attributeKey => $attribute) {
                $value = $attribute;
                $attribute = is_numeric($attributeKey) ? $attribute : $attributeKey;

                $attributeValue = $attribute != 'price'
                    ? ($product->hasData($attribute)
                        ? $product->getData($attribute)
                        : 'N/A')
                    : ($product->getDataFieldConfig('price')['source']->getPreset() !== null
                        ? $product->getDataFieldConfig('price')['source']->getPreset()['compare_price']
                        : number_format($product->getPrice(), 2));

                $attribute = is_numeric($attributeKey) ? 'info' : 'attribute';
                \PHPUnit_Framework_Assert::assertEquals(
                    $attributeValue,
                    $comparePage->getCompareProductsBlock()->{'getProduct' . ucfirst($attribute)}($key + 1, $value),
                    'Product "' . $product->getName() . '" with ' . $attribute . ' "' . $attributeValue
                    . '" is absent on compare product page.'
                );
            }
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return '"Compare Product" page has valid data for all products.';
    }
}
