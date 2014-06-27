<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AssertForm;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertProductForm
 */
class AssertProductForm extends AssertForm
{
    protected $sortFields = [
        'giftcard_amounts::price'
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

        $fixtureData = $this->prepareFixtureData($product->getData(), $this->sortFields);
        $formData = $this->prepareFormData($productPage->getForm()->getData($product), $this->sortFields);
        $error = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertTrue(null === $error, $error);
    }

    /**
     * Prepares fixture data for comparison
     *
     * @param array $data
     * @param $sortFields
     * @return array
     */
    protected function prepareFixtureData(array $data, $sortFields = null)
    {
        if (isset($data['website_ids']) && !is_array($data['website_ids'])) {
            $data['website_ids'] = [$data['website_ids']];
        }

        if ($sortFields) {
            $this->sortData($data, $sortFields);
        }
        return $data;
    }

    /**
     * Prepares form data for comparison
     *
     * @param array $data
     * @param $sortFields
     * @return array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function prepareFormData(array $data, $sortFields)
    {
        if ($sortFields) {
            $this->sortData($data, $sortFields);
        }
        return $data;
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
