<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductForm;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertDownloadableProductForm
 */
class AssertDownloadableProductForm extends AssertProductForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that downloadable product data on edit page equals to passed from fixture
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
        $filter = ['sku' => $product->getData('sku')];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);

        $fields = $this->prepareFixtureData($product);
        $fields = $this->convertDownloadableArray($fields);

        $fieldsForm = $productPage->getForm()->getData($product);
        \PHPUnit_Framework_Assert::assertEquals($fields, $fieldsForm, 'Form data not equals fixture data.');
    }

    /**
     * Sort downloadable array
     *
     * @param array $fields
     * @return array
     */
    protected function sortDownloadableArray(&$fields)
    {
        usort(
            $fields,
            function ($a, $b) {
                return $a['sort_order'] - $b['sort_order'];
            }
        );
    }

    /**
     * Convert fixture array
     *
     * @param array $fields
     * @return array
     */
    protected function convertDownloadableArray(array $fields)
    {
        if (isset($fields['downloadable_links']['downloadable']['link'])) {
            $this->sortDownloadableArray(
                $fields['downloadable_links']['downloadable']['link']
            );
        }
        if (isset($fields['downloadable_sample']['downloadable']['sample'])) {
            $this->sortDownloadableArray(
                $fields['downloadable_sample']['downloadable']['sample']
            );
        }

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $fields[$key] = $this->convertDownloadableArray($value);
            } else {
                if ($key == "special_price") {
                    $fields[$key] = [$key => $fields[$key]];
                }
            }
        }
        return $fields;
    }

    /**
     * Text of Visible in product form assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data equal the fixture data.';
    }
}
