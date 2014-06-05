<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureFactory;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductTemplate;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;
use Mtf\System\Config;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class AssertProductAttributeOnProductForm
 */
class AssertProductAttributeOnProductForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Add this attribute to Default attribute Template. Create product and Assert that created attribute
     * is displayed on product form (Products > Inventory > Catalog)
     *
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductSetIndex $productSet
     * @param CatalogProductSetEdit $productSetEdit
     * @param CatalogProductTemplate $productTemplate
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductEdit $productEdit
     * @param mixed $product
     * @param CatalogProductAttribute|null $productAttribute
     * @return void
     */
    public function processAssert
    (
        FixtureFactory $fixtureFactory,
        CatalogProductSetIndex $productSet,
        CatalogProductSetEdit $productSetEdit,
        CatalogProductTemplate $productTemplate,
        CatalogProductIndex $productGrid,
        CatalogProductAttribute $attribute,
        CatalogProductEdit $productEdit,
        $product,
        CatalogProductAttribute $productAttribute = null
    ) {
        $filterAttribute = [
            'set_name' => $productTemplate->getAttributeSetName(),
        ];
        $productSet->open();
        $productSet->getBlockAttributeSetGrid()->searchAndOpen($filterAttribute);

        $attributeLabel = ($productAttribute) ? $attribute->getFrontendLabel() : $attribute->getAttributeCode();
        $productSetEdit->getNewAttributes()->moveAttribute($attributeLabel);
        $productSetEdit->getPageActions()->save();

        $product = explode('::', $product);
        $attributeId = $this->getAttributeId($attributeLabel);
        $product = $fixtureFactory->createByCode(
            $product[0],
            [
                'dataSet' => $product[1],
                'data' => [
                    'attribute_set_id' => $productTemplate->getData('id'),
                    'configurable_attributes_data' => [
                        $attributeId => [
                            'attribute_id' => $attributeId,
                            'code' => $attribute->getData('frontend_label'),
                            'label' => $attribute->getData('frontend_label'),
                            'id' => 'new',
                        ]
                    ]
                ]
            ]
        );
        $product->persist();

        $filterProduct = [
            'sku' => $product->getSku(),
        ];
        $productGrid->open();
        $productGrid->getProductGrid()->searchAndOpen($filterProduct);

        $frontendLabel = ($productAttribute) ? $productAttribute->getFrontendLabel() : $attribute->getFrontendLabel();
        \PHPUnit_Framework_Assert::assertTrue(
            $productEdit->getForm()->checkAttributeLabel($frontendLabel),
            "Product Attribute is absent on Product form."
        );
    }

    /**
     * Get attribute id by attributeLabel
     *
     * @param string $attributeLabel
     * @return int|null
     */
    protected function getAttributeId($attributeLabel)
    {
        $filter = ['attribute_code' => $attributeLabel];
        $url = $_ENV['app_backend_url'] . 'catalog/product_attribute/index/filter/' . $this->encodeFilter($filter);
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        preg_match('`<tr.*?http.*?attribute_id\/(\d*?)\/`', $response, $match);
        return empty($match[1]) ? null : $match[1];
    }

    /**
     * Encoded filter parameters
     *
     * @param array $filter
     * @return string
     */
    protected function encodeFilter(array $filter)
    {
        $result = [];
        foreach ($filter as $name => $value) {
            $result[] = "{$name}={$value}";
        }
        $result = implode('&', $result);

        return base64_encode($result);
    }

    /**
     * Text of Product Attribute is present on the Product form.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product Attribute is present on Product form.';
    }
}
