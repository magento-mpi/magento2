<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\TestCase;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Flow:
 *
 * Preconditions:
 * 1. Two simple products are created.
 * 2. Configurable attribute with two options is created.
 * 3. Configurable attribute added to default template.
 * 4. Configurable product is created.
 *
 * Steps:
 * 1. Log in to backend.
 * 2. Open Products -> Catalog.
 * 3. Search and open configurable product from preconditions.
 * 4. Fill in data according to dataSet.
 * 5. Save product.
 * 6. Perform all assertions.
 *
 * @group Configurable_Product_(MX)
 * @ZephyrId MAGETWO-29916
 */
class UpdateConfigurableProductEntityTest extends Injectable
{
    /**
     * Catalog product index page.
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * Catalog product edit page.
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Catalog product attributes.
     *
     * @var CatalogProductAttribute
     */
    protected $deletedAttributes = [];

    /**
     * Injection data.
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductEdit $catalogProductEdit
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        FixtureFactory $fixtureFactory
    ) {
        $this->catalogProductIndex = $catalogProductIndex;
        $this->catalogProductEdit = $catalogProductEdit;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Update  configurable product.
     *
     * @param ConfigurableProductInjectable $initialProduct
     * @param ConfigurableProductInjectable $product
     * @param string $attributeTypeAction
     * @return array
     */
    public function test(
        ConfigurableProductInjectable $initialProduct,
        ConfigurableProductInjectable $product,
        $attributeTypeAction
    ) {
        // Preconditions:
        $initialProduct->persist();

        // Steps:
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductGrid()->searchAndOpen(['sku' => $initialProduct->getSku()]);

        if ($attributeTypeAction != 'deleteAll') {
            $product = $this->prepareProduct($initialProduct, $product, $attributeTypeAction);
        } else {
            $this->deletedAttributes = $initialProduct->getDataFieldConfig('configurable_attributes_data')['source']
                ->getAttributes();
        }
        $this->updateProduct($product);
        $this->catalogProductEdit->getFormPageActions()->save($product);

        return ['product' => $product, 'deletedProductAttributes' => $this->deletedAttributes];
    }

    /**
     * Prepare new product for update.
     *
     * @param ConfigurableProductInjectable $initialProduct
     * @param ConfigurableProductInjectable $product
     * @param string $attributeTypeAction
     * @return ConfigurableProductInjectable
     */
    protected function prepareProduct(
        ConfigurableProductInjectable $initialProduct,
        ConfigurableProductInjectable $product,
        $attributeTypeAction
    ) {
        $dataProduct = $product->getData();
        $dataInitialProduct = $initialProduct->getData();
        $oldMatrix = [];

        if ($attributeTypeAction == 'deleteLast') {
            array_pop($dataInitialProduct['configurable_attributes_data']['attributes_data']);
            $attributes = $initialProduct->getDataFieldConfig('configurable_attributes_data')['source']
                ->getAttributes();
            $this->deletedAttributes[] = array_pop($attributes);
        }

        $attributesData = $dataInitialProduct['configurable_attributes_data']['attributes_data'];
        if ($attributeTypeAction == 'addOptions') {
            $oldMatrix = $dataInitialProduct['configurable_attributes_data']['matrix'];
            $this->addOptions($attributesData, $dataProduct['configurable_attributes_data']['attributes_data']);
        } else {
            $this->addAttributes($attributesData, $dataProduct['configurable_attributes_data']['attributes_data']);
        }

        $dataProduct['configurable_attributes_data'] = [
            'attributes_data' => $attributesData,
            'matrix' => $oldMatrix
        ];

        if ($product->hasData('category_ids')) {
            $dataProduct['category_ids']['category'] = $product->getDataFieldConfig('category_ids')['source']
                ->getCategories()[0];
        }

        return $this->fixtureFactory->createByCode('configurableProductInjectable', ['data' => $dataProduct]);
    }

    /**
     * Add options.
     *
     * @param array $attributes
     * @param array $data
     * @return void
     */
    protected function addOptions(array &$attributes, array $data)
    {
        foreach ($attributes as $key => $attribute) {
            if (isset($data[$key])) {
                $index = count($attribute['options']);
                foreach ($data[$key]['options'] as $newOption) {
                    $attributes[$key]['options']['option_key_' . $index] = $newOption;
                    $index++;
                }
            }
        }
    }

    /**
     * Add attributes.
     *
     * @param array $attributes
     * @param array $data
     * @return void
     */
    protected function addAttributes(array &$attributes, array $data)
    {
        $index = count($attributes);
        foreach ($data as $attribute) {
            $attributes['attribute_key_' . $index] = $attribute;
            $index++;
        }
    }

    /**
     * Update product.
     *
     * @param ConfigurableProductInjectable $product
     * @return void
     */
    protected function updateProduct(ConfigurableProductInjectable $product)
    {
        $productForm = $this->catalogProductEdit->getProductForm();
        $productForm->openTab('variations');
        $productForm->getTabElement('variations')->deleteAttributes();
        $this->catalogProductEdit->getProductForm()->fill($product);
    }
}
