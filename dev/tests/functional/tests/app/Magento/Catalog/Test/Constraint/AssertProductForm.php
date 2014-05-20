<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;

/**
 * Class AssertProductForm
 *
 * Assert that displayed product data on edit page equals passed from fixture
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
     * @param InjectableFixture $product
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductNew $productPage
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CatalogProductIndex $productGrid,
        CatalogProductNew $productPage
    ) {
        $filter = ['sku' => $product->getData('sku')];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);
        $fields = $this->convertArray($product->getData());
        $fieldsForm = $productPage->getForm()->getData($product);
        \PHPUnit_Framework_Assert::assertEquals($fields, $fieldsForm, 'Form data not equals fixture data');
    }

    /**
     * Convert fixture array
     *
     * @param array $fields
     * @return array
     */
    public function convertArray(array $fields)
    {
        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $fields[$key] = $this->convertArray($value);
            } else {
                if ($value === null) {
                    unset($fields[$key]);
                } elseif ($key == "price" || $key == "special_price") {
                    $fields[$key] = sprintf('%1.2f', $fields[$key]);
                } elseif ($key == "qty" || $key == "stock_data_qty") {
                    $fields[$key] = sprintf('%1.4f', $fields[$key]);
                } elseif ($key == "stock_data_qty") {
                    $fields[$key] = sprintf('%1.4f', $fields[$key]);
                } elseif ($key == "stock_data_use_config_min_qty" && $value == "No") {
                    $fields[$key] = ($value == "No") ? false : true;
                } elseif ($key == "is_require") {
                    $fields[$key] = ($value == "Yes" || $value == "1" || $value == true) ? 1 : 0;
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
