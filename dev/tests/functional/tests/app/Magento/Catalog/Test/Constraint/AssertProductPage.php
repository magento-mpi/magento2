<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertProductPage
 */
class AssertProductPage extends AbstractConstraint
{
    /**
     * Product fixture
     *
     * @var FixtureInterface
     */
    protected $product;

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assertion that the product page is displayed correctly
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        $this->product = $product;
        // TODO fix initialization url for frontend page
        //Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();

        //Process assertions
        $this->assertOnProductView($catalogProductView);
    }

    /**
     * Assert prices on the product view page
     *
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    protected function assertOnProductView(CatalogProductView $catalogProductView)
    {
        $viewBlock = $catalogProductView->getViewBlock();
        $price = $viewBlock->getProductPriceBlock()->getPrice();
        $errorsMessages = [
            'name'              => '- product name on product view page is not correct.',
            'sku'               => '- product sku on product view page is not correct.',
            'regular_price'     => '- product regular price on product view page is not correct.',
            'short_description' => '- product short description on product view page is not correct.',
            'description'       => '- product description on product view page is not correct.'
        ];
        $dataOnPage = [
            'name'          => $viewBlock->getProductName(),
            'sku'           => $viewBlock->getProductSku(),
            'regular_price' => $price['price_regular_price']
        ];
        $compareData = [
            'name'          => $this->product->getName(),
            'sku'           => $this->product->getSku(),
            'regular_price' => number_format($this->product->getData('price'), 2),

        ];

        if ($productShortDescription = $this->product->getShortDescription()) {
            $compareData['short_description'] = $productShortDescription;
            $dataOnPage['short_description'] = $viewBlock->getProductShortDescription();
        }
        if ($productDescription = $this->product->getDescription()) {
            $compareData['description'] = $productDescription;
            $dataOnPage['description'] = $viewBlock->getProductDescription();
        }

        $badValues = array_diff($dataOnPage, $compareData);
        $errorsMessages = array_merge(
            $this->assertSpecialPrice($price),
            array_intersect_key($errorsMessages, array_keys($badValues))
        );

        \PHPUnit_Framework_Assert::assertTrue(
            empty($errorsMessages),
            PHP_EOL . 'Found the following errors:' . PHP_EOL
            . implode(' ' . PHP_EOL, $errorsMessages)
        );
    }

    /**
     * Checking the special product price
     *
     * @param array $price
     * @return array
     */
    protected function assertSpecialPrice(array $price)
    {
        $priceComparing = false;
        if ($specialPrice = $this->product->getSpecialPrice()) {
            $priceComparing = $specialPrice;
        }
        if ($groupPrice = $this->product->getGroupPrice()) {
            $groupPrice = reset($groupPrice);
            $priceComparing = $groupPrice['price'];
        }
        if ($priceComparing && isset($price['price_special_price'])
            && number_format($priceComparing, 2) !== $price['price_special_price']
        ) {
            return ['special_price' => '- product special price on product view page is not correct.'];
        }

        return [];
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product on product view page is not correct.';
    }
}
