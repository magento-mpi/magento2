<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Constraint\AssertProductForm;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Class AssertDownloadableProductForm
 * Assert that downloadable product data on edit page equals to passed from fixture
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
        $filter = ['sku' => $product->getData('sku')];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);

        $fields = $this->convertDownloadableArray($this->prepareFixtureData($product));

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
            function ($row1, $row2) {
                if ($row1['sort_order'] == $row2['sort_order']) {
                    return 0;
                }
                return ($row1['sort_order'] < $row2['sort_order']) ? -1 : 1;
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
