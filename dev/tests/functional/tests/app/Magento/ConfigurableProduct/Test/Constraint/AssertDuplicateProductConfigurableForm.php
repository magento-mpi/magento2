<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Constraint\AssertDuplicateProductForm;
use Magento\ConfigurableProduct\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertDuplicateProductConfigurableForm
 */
class AssertDuplicateProductConfigurableForm extends AssertDuplicateProductForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert form data equals duplicate product configurable data
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
        $filter = ['sku' => $product->getSku() . '-1'];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);

        $form = $productPage->getForm();
        $formData = $form->getData($product);
        $fixtureData = $this->prepareFixtureData($product);
        $attributes = $fixtureData['configurable_attributes_data']['attributes_data'];
        unset($fixtureData['configurable_attributes_data']);
        $errors = $this->compareArray($fixtureData, $formData);

        foreach ($attributes as $attribute) {
            if (!$form->findAttribute($attribute['title'])) {
                $errors[] = '- attribute "' . $attribute['title'] . '" was not found in the form of product';
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            "These data must be equal to each other:\n" . implode("\n", $errors)
        );
    }
}
