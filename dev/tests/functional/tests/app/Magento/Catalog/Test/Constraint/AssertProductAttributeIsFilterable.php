<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Check whether the attribute filter is displayed on the frontend in Layered navigation.
 */
class AssertProductAttributeIsFilterable extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Check whether the attribute filter is displayed on the frontend in Layered navigation.
     *
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductEdit $catalogProductEdit
     * @param InjectableFixture $product
     * @param CatalogProductAttribute $attribute
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogCategoryView $catalogCategoryView,
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        InjectableFixture $product,
        CatalogProductAttribute $attribute,
        Browser $browser
    ) {
        $catalogProductIndex->open()->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);
        $catalogProductEdit->getProductForm()->getCustomAttributeBlock($attribute)->setValue();

        $categories = $product->getDataFieldConfig('category_ids')['source']->getCategories();
        $browser->open($_ENV['app_frontend_url'] . reset($categories)->getUrlKey() . '.html');
        $label = $attribute->hasData('manage_frontend_label')
            ? $attribute->getManageFrontendLabel()
            : $attribute->getFrontendLabel();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array($label, array_keys($catalogCategoryView->getLayeredNavigationBlock()->getAvailableOptions())),
            'Attribute is absent in layered navigation on category page.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is present in layered navigation on category page.';
    }
}
