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
        'name' => 'Name',
        'price' => 'Price',
        'sku' => ['Sku' => 'SKU'],
        'description' => ['Description' => 'Description'],
        'short_description' => ['ShortDescription' => 'Short Description']
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
                $value = '';
                if (is_array($attribute)) {
                    $value = $attribute[key($attribute)];
                    $attribute = key($attribute);
                }

                $attributeValue = $attributeKey != 'price'
                    ? ($product->hasData($attributeKey)
                        ? $product->{'get' . $attribute}()
                        : 'N/A')
                    : ($product->getDataFieldConfig('price')['source']->getPreset() !== null
                        ? number_format($product->getDataFieldConfig('price')['source']->getPreset(), 2)
                        : number_format($product->getPrice(), 2));

                $attribute = $value != '' ? 'Attribute' : $attribute;
                \PHPUnit_Framework_Assert::assertEquals(
                    $attributeValue,
                    $comparePage->getCompareProductsBlock()->{'getProduct' . $attribute}($key + 1, $value),
                    'This product "' . $product->getName() . '" with ' . $attribute . ' "' . $attributeValue
                    . '" is not in compare product page.'
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
