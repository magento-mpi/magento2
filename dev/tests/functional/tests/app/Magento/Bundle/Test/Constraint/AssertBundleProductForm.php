<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Constraint\AssertProductForm;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertBundleProductForm
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
        $filter = ['sku' => $product->getSku()];
        $productGrid->open();
        $productGrid->getProductGrid()->searchAndOpen($filter);

        $formData = $productPage->getForm()->getData($product);
        $fixtureData = $this->prepareFixtureData($product->getData());
        $errors = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Prepares fixture data for comparison
     *
     * @param array $data
     * @param array $sortFields [optional]
     * @return array
     */
    protected function prepareFixtureData(array $data, array $sortFields = [])
    {
        $data = $this->prepareSpecialPriceArray($data);
        $data['bundle_selections'] = $this->prepareBundleOptions(
            $data['bundle_selections']['bundle_options']
        );

        return parent::prepareFixtureData($data, $sortFields);
    }

    /**
     * Prepare special price array for Bundle product
     *
     * @param array $fields
     * @return array
     */
    protected function prepareSpecialPriceArray(array $fields)
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

    /**
     * Prepare Bundle Options array from preset
     *
     * @param array $bundleSelections
     * @return array
     */
    protected function prepareBundleOptions(array $bundleSelections)
    {
        foreach ($bundleSelections as &$item) {
            foreach ($item['assigned_products'] as &$selection) {
                $selection['data']['getProductName'] = $selection['search_data']['name'];
                $selection = $selection['data'];
            }
        }

        return $bundleSelections;
    }
}
