<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductPage;

/**
 * Class AssertConfigurableProductPage
 * Assert that displayed product data on product page(front-end) equals passed from fixture:
 * 1. Product Name
 * 2. Price
 * 3. SKU
 * 4. Description
 * 5. Short Description
 * 6. Attributes
 */
class AssertConfigurableProductPage extends AssertProductPage
{
    /**
     * Product view page class on frontend
     *
     * @var string
     */
    protected $productViewClass = 'Magento\ConfigurableProduct\Test\Page\Product\CatalogProductView';

    /**
     * Verify displayed product data on product page(front-end) equals passed from fixture
     *
     * @return array
     */
    protected function verify()
    {
        $errors = parent::verify();
        $errors[] = $this->verifyAttributes();

        return array_filter($errors);
    }

    /**
     * Verify displayed product attributes on product page(front-end) equals passed from fixture
     *
     * @return string|null
     */
    protected function verifyAttributes()
    {
        //
    }
}
