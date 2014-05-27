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
 * Class AssertCustomOptionsOnProductPage
 * Assert that displayed product custom options data on product page equals passed from fixture
 */
class AssertCustomOptionsOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Product fixture
     *
     * @var FixtureInterface
     */
    protected $product;

    /**
     * Assertion that commodity options are displayed correctly
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        $this->product = $product;
        // TODO fix initialization url for frontend page
        // Open product view page
        $catalogProductView->init($this->product);
        $catalogProductView->open();
        $customOptions = $catalogProductView->getCustomOptionsBlock()->getOptions();
        $compareOptions = $this->prepareOptionArray($this->product->getCustomOptions());

        \PHPUnit_Framework_Assert::assertEquals(
            $compareOptions,
            $customOptions,
            'Incorrect display of custom product options on the product page.'
        );
    }

    /**
     * Preparation options before comparing
     *
     * @param array $options
     * @return array
     */
    protected function prepareOptionArray(array $options)
    {
        $result = [];
        $productPrice = $this->product->hasData('group_price')
            ? $this->product->getPrice()
            : $this->product->getGroupPrice()[0]['price'];

        $placeholder = ['Yes' => true, 'No' => false];
        foreach ($options as $option) {
            $result[$option['title']]['is_require'] = $placeholder[$option['is_require']];
            $result[$option['title']]['title'] = $option['title'];
            $result[$option['title']]['price'] = [];
            foreach ($option['options'] as $optionValue) {
                if ($optionValue['price_type'] === 'Percent') {
                    $optionValue['price'] = $productPrice / 100 * $optionValue['price'];
                }
                $result[$option['title']]['price'][] = number_format($optionValue['price'], 2);
                foreach ($option['options'] as $itemOption) {
                    $result[$option['title']]['value'][] = $itemOption['title']
                        . ' +$' . number_format($optionValue['price'], 2);
                }
            }
        }

        return $result;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Value of custom option on the page is correct.';
    }
}
