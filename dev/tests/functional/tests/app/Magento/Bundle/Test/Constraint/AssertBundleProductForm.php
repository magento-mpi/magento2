<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Constraint\AssertProductForm;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertBundleProductForm
 * Assert that bundle product data on edit page equals to passed from fixture
 */
class AssertBundleProductForm extends AssertProductForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Formatting options for array values
     *
     * @var array
     */
    protected $specialArray = [
        'special_price' => [],
        'price_view' => [],
        'special_from_date' => [
            'type' => 'date'
        ],
        'special_to_date' => [
            'type' => 'date'
        ]
    ];

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
        $this->formattingOptions += [
            'selection_qty' => [
                'decimals' => 4,
                'dec_point' => '.',
                'thousands_sep' => ''
            ],
            'selection_price_value' => [
                'decimals' => 2,
                'dec_point' => '.',
                'thousands_sep' => ''
            ]
        ];

        $filter = ['sku' => $product->getSku()];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);

        $formData = $productPage->getForm()->getData($product);
        $fixtureData = $this->convertSpecialArray($this->prepareFixtureData($product));
        if (isset($fixtureData['status'])) {
            $fixtureData['status'] = ($fixtureData['status'] == 'Yes') ? 'Product online' : 'Product offline';
        }
        $fixtureData['bundle_selections'] = $this->prepareBundleOptions($formData['bundle_selections']);

        $errors = $this->compareArray($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($errors),
            "These data must be equal to each other:\n" . implode("\n", $errors)
        );
    }

    /**
     * Prepare Bundle Options array from preset
     *
     * @param array $bundleSelections
     * @return array|null
     */
    protected function prepareBundleOptions(array $bundleSelections)
    {
        if (!isset($bundleSelections['preset'])) {
            return $bundleSelections;
        }
        $preset = $bundleSelections['preset'];
        $products = $bundleSelections['products'];
        foreach ($preset as & $item) {
            foreach ($item['assigned_products'] as $productIncrement => $selection) {
                if (!isset($products[$productIncrement])) {
                    break;
                }
                $newData = [];
                /** @var InjectableFixture $fixture */
                $fixture = $products[$productIncrement];
                $newData['getProductName'] = $fixture->getData('name');
                $newData['selection_qty'] = $selection['data']['selection_qty'];
                if ($this->arguments['product']->getPriceType() == 'Fixed') {
                    $newData['selection_price_value'] = $selection['data']['selection_price_value'];
                    $newData['selection_price_type'] = $selection['data']['selection_price_type'];
                }
                $item['assigned_products'][$productIncrement] = $newData;
            }
        }
        return $preset;
    }

    /**
     * Convert fixture array to correct format
     *
     * @param array $fields
     * @return array
     */
    protected function convertSpecialArray(array $fields)
    {
        foreach ($this->specialArray as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (isset($value['type']) && $value['type'] == 'date') {
                    $fields[$key] = vsprintf('%d/%d/%d', explode('/', $fields[$key]));
                }
                $fields[$key] = [$key => $fields[$key]];
            }
        }
        return $fields;
    }
}
