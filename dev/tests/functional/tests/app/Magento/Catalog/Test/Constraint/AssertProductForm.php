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
     * Formatting options for numeric values
     *
     * @var array
     */
    protected $formattingOptions = [
        'price' => [
            'decimals' => 2,
            'dec_point' => '.',
            'thousands_sep' => ''
        ],
        'qty' => [
            'decimals' => 4,
            'dec_point' => '.',
            'thousands_sep' => ''
        ],
        'weight' => [
            'decimals' => 4,
            'dec_point' => '.',
            'thousands_sep' => ''
        ]
    ];

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
            "These data must be equal to each other:\n" . implode("\n", $errors)
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

        array_walk_recursive(
            $compareData,
            function (&$item, $key, $formattingOptions) {
                if (isset($formattingOptions[$key])) {
                    $item = number_format(
                        $item,
                        $formattingOptions[$key]['decimals'],
                        $formattingOptions[$key]['dec_point'],
                        $formattingOptions[$key]['thousands_sep']
                    );
                }
            },
            $this->formattingOptions
        );

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
        $keysDiff = array_diff(array_keys($fixtureData), array_keys($formData));
        if (!empty($keysDiff)) {
            return ['- fixture data do not correspond to form data in composition.'];
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
                    "- error key -> '{$key}' : error value ->  '{$fixtureValue}' does not equal -> '{$formValue}'."
                ]);
            }
        }

        return $errors;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data equal the fixture data.';
    }
}
