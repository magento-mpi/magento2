<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestStep;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeNew;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Mtf\TestStep\TestStepInterface;

/**
 * Fill attribute form on attribute page.
 */
class FillAttributeFormStep implements TestStepInterface
{
    /**
     * CatalogProductAttribute fixture.
     *
     * @var CatalogProductAttribute
     */
    protected $attribute;

    /**
     * Catalog product attribute edit page.
     *
     * @var CatalogProductAttributeNew
     */
    protected $attributeNew;

    /**
     * @constructor
     * @param CatalogProductAttribute $productAttribute
     * @param CatalogProductAttributeNew $attributeNew
     */
    public function __construct(CatalogProductAttribute $productAttribute, CatalogProductAttributeNew $attributeNew)
    {
        $this->attribute = $productAttribute;
        $this->attributeNew = $attributeNew;
    }

    /**
     * Fill custom attribute form on attribute page.
     *
     * @return array
     */
    public function run()
    {
        $this->attributeNew->getAttributeForm()->fill($this->attribute);
        return ['attribute' => $this->attribute];
    }
}
