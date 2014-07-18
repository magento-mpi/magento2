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
     * Error messages
     *
     * @var array
     */
    protected $errorsMessages = [
        'name' => '- product name on product view page is not correct.',
        'sku' => '- product sku on product view page is not correct.',
        'regular_price' => '- product regular price on product view page is not correct.',
        'short_description' => '- product short description on product view page is not correct.',
        'description' => '- product description on product view page is not correct.'
    ];

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

        $data = $this->prepareData($catalogProductView);
        $badValues = array_diff($data['onPage'], $data['fixture']);
        $errors = array_intersect_key($this->errorsMessages, array_keys($badValues));
        $errors += $this->verifySpecialPrice($catalogProductView);
        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            PHP_EOL . 'Found the following errors:' . PHP_EOL . implode(' ' . PHP_EOL, $this->errorsMessages)
        );
    }

    /**
     * Prepare array for assert
     *
     * @param CatalogProductView $catalogProductView
     * @return array
     */
    protected function prepareData(CatalogProductView $catalogProductView)
    {
        $viewBlock = $catalogProductView->getViewBlock();
        $priceBlock = $viewBlock->getProductPriceBlock();
        $data = [
            'onPage' => [
                'name' => $viewBlock->getProductName(),
                'sku' => $viewBlock->getProductSku(),
            ],
            'fixture' => [
                'name' => $this->product->getName(),
                'sku' => $this->product->getSku(),
            ]
        ];

        if ($this->product->hasData('price')) {
            $data['fixture']['regular_price'] = number_format($this->product->getPrice(), 2);
            $data['onPage']['regular_price'] = $priceBlock->isVisible()
                ? $priceBlock->getPrice()['price_regular_price']
                : null;
        }
        if ($productShortDescription = $this->product->getShortDescription()) {
            $data['fixture']['short_description'] = $productShortDescription;
            $data['onPage']['short_description'] = $viewBlock->getProductShortDescription();
        }
        if ($productDescription = $this->product->getDescription()) {
            $data['fixture']['description'] = $productDescription;
            $data['onPage']['description'] = $viewBlock->getProductDescription();
        }

        return $data;
    }

    /**
     * Checking the special product price
     *
     * @param CatalogProductView $catalogProductView
     * @return array
     */
    protected function verifySpecialPrice(CatalogProductView $catalogProductView)
    {
        $priceBlock = $catalogProductView->getViewBlock()->getProductPriceBlock();
        $price = $priceBlock->isVisible() ? $priceBlock->getPrice() : ['price_special_price' => null];
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
