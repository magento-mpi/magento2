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
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Class AssertProductForm
 */
class AssertProductForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert form data equals fixture data
     *
     * @param FixtureInterface $product
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductEdit $productPage
     * @return void
     */
    public function processAssert(
        FixtureInterface $product,
        CatalogProductIndex $productGrid,
        CatalogProductEdit $productPage
    ) {
        $filter = ['sku' => $product->getSku()];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);

        $dataInForm = $productPage->getForm()->getData($product);
        $compareData = $this->prepareFixtureData($product);

        $errors = $this->compareArray($dataInForm, $compareData);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($errors),
            "These data must be equal to each other:\n" . implode("\n - ", $errors)
        );
    }

    /**
     * Prepares and returns data to the fixture, ready for comparison
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareFixtureData(FixtureInterface $product)
    {
        $compareData = $product->getData();
        $compareData = array_filter($compareData);

        $compareData['price'] = $this->priceFormat($compareData['price']);
        $compareData['qty'] = number_format($compareData['qty'], 4, '.', '');
        $compareData['weight'] = number_format($compareData['weight'], 4, '.', '');
        unset($compareData['url_key']);

        if (!empty($compareData['tier_price'])) {
            foreach ($compareData['tier_price'] as &$value) {
                $value['price'] = $this->priceFormat($value['price']);
            }
            unset($value);
        }
        if (!empty($compareData['group_price'])) {
            foreach ($compareData['group_price'] as &$value) {
                $value['price'] = $this->priceFormat($value['price']);
            }
            unset($value);
        }
        if (!empty($compareData['custom_options'])) {
            $placeholder = ['Yes' => true, 'No' => false];
            foreach ($compareData['custom_options'] as &$option) {
                $option['is_require'] = $placeholder[$option['is_require']];
                foreach ($option['options'] as &$value) {
                    $value['price'] = $this->priceFormat($value['price']);
                }
                unset($value);
            }
            unset($option);
        }

        return $compareData;
    }

    /**
     * Comparison of multidimensional arrays
     *
     * @param array $arrayOne
     * @param array $arrayTwo
     * @return array
     */
    protected function compareArray(array $arrayOne, array $arrayTwo)
    {
        $errors = [];
        ksort($arrayOne);
        ksort($arrayTwo);
        if (array_keys($arrayOne) !== array_keys($arrayTwo)) {
            return ['arrays do not correspond to each other in composition'];
        }

        foreach ($arrayOne as $key => $value) {
            if (is_array($value) && is_array($arrayTwo[$key])
                && ($innerErrors = $this->compareArray($value, $arrayTwo[$key])) && !empty($innerErrors)
            ) {
                $errors = array_merge($errors, $innerErrors);
            } elseif ($value != $arrayTwo[$key]) {
                $paceholderOne = empty($value) ? '<empty-value>' : $value;
                $paceholderTwo = empty($arrayTwo[$key]) ? '<empty-value>' : $arrayTwo[$key];
                $errors = array_merge($errors, [
                    "error key -> {$key} : error value ->  {$paceholderOne} does not equal -> {$paceholderTwo}"
                ]);
            }
        }

        return $errors;
    }

    /**
     * Formatting prices
     *
     * @param $price
     * @return string
     */
    protected function priceFormat($price)
    {
        return number_format($price, 2, '.', '');
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data equal the fixture data.';
    }
}
