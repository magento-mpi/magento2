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

        $fixtureData = $productPage->getForm()->getData($product);
        $formData = $this->prepareFixtureData($product);

        $errors = $this->compareArray($fixtureData, $formData);
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
     * @param array $fixtureData
     * @param array $formData
     * @return array
     */
    protected function compareArray(array $fixtureData, array $formData)
    {
        $errors = [];
        ksort($fixtureData);
        ksort($formData);
        if (array_keys($fixtureData) !== array_keys($formData)) {
            return ['arrays do not correspond to each other in composition'];
        }

        foreach ($fixtureData as $key => $value) {
            if (is_array($value) && is_array($formData[$key])
                && ($innerErrors = $this->compareArray($value, $formData[$key])) && !empty($innerErrors)
            ) {
                $errors = array_merge($errors, $innerErrors);
            } elseif ($value != $formData[$key]) {
                $fixtureValue = empty($value) ? '<empty-value>' : $value;
                $formValue = empty($formData[$key]) ? '<empty-value>' : $formData[$key];
                $errors = array_merge($errors, [
                    "error key -> '{$key}' : error value ->  '{$fixtureValue}' does not equal -> '{$formValue}'"
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
