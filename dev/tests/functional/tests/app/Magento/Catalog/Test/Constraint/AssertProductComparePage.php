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
    protected $attributeProduct = ['name', 'price', 'sku', 'description', 'short_description'];

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
     * @param array $productsPrice
     * @return void
     */
    public function processAssert(
        array $products,
        CatalogProductCompare $comparePage,
        CmsIndex $cmsIndex,
        array $productsPrice
    ) {
        $cmsIndex->getLinksBlock()->openLink("Compare Products");
        foreach ($products as $key => $product) {
            foreach ($this->attributeProduct as $attribute) {
                $attributeName = $this->attributeNameConvert($attribute);
                $attributeValue = $attribute != 'price'
                    ? ($product->hasData($attribute)
                        ? $product->{'get' . $attributeName}()
                        : 'N/A')
                    : $productsPrice[$key];
                \PHPUnit_Framework_Assert::assertEquals(
                    $attributeValue,
                    $comparePage->getCompareProductsBlock()->{'getProduct' . $attributeName}($key + 1),
                    'This product with ' . $attribute . ' "' . $attributeValue . '" is not in compare product page.'
                );
            }
        }
    }

    /**
     * Convert attribute name
     *
     * @param string $optionName
     * @return string
     */
    protected function attributeNameConvert($optionName)
    {
        $optionName = ucfirst($optionName);
        if ($end = strpos($optionName, '_')) {
            $optionName = substr($optionName, 0, $end) . ucfirst(substr($optionName, ($end + 1)));
        }
        return $optionName;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'This "Compare Product" page is correct.';
    }
}
