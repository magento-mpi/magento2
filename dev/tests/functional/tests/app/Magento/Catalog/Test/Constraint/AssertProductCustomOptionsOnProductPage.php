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
 * Class AssertProductCustomOptionsOnProductPage
 */
class AssertProductCustomOptionsOnProductPage extends AbstractConstraint
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
        // Prepare data
        $customOptions = $catalogProductView->getCustomOptionsBlock()->getOptions();
        foreach ($customOptions as &$option) {
            unset($option['value']);
        }
        unset($option);
        $compareOptions = $this->prepareOptionArray($this->product->getCustomOptions());
        $customOptions = $this->dataSortByKey($customOptions);
        $compareOptions = $this->dataSortByKey($compareOptions);

        \PHPUnit_Framework_Assert::assertEquals(
            $customOptions,
            $compareOptions,
            'Incorrect display of custom product options on the product page.'
        );
    }

    protected function dataSortByKey(array $data)
    {
        foreach ($data as &$item) {
            ksort($item);
        }
        unset($item);
        return $data;
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
            ? $this->product->getGroupPrice()[0]['price']
            : $this->product->getPrice();

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
            }
        }

        return $result;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Value of custom option on the page is correct.';
    }
}
