<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestStep;

use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Mtf\TestStep\TestStepInterface;

/**
 * Move Attribute To AttributeSet.
 */
class MoveAttributeToAttributeSetStep implements TestStepInterface
{
    /**
     * Catalog ProductSet Index page.
     *
     * @var CatalogProductSetIndex
     */
    protected $catalogProductSetIndex;

    /**
     * Catalog ProductSet Edit page.
     *
     * @var CatalogProductSetEdit
     */
    protected $catalogProductSetEdit;

    /**
     * Catalog Product Attribute fixture.
     *
     * @var CatalogProductAttribute
     */
    protected $attribute;

    /**
     * Catalog AttributeSet fixture.
     *
     * @var CatalogAttributeSet
     */
    protected $productTemplate;

    /**
     * @constructor
     * @param CatalogProductSetIndex $catalogProductSetIndex
     * @param CatalogProductSetEdit $catalogProductSetEdit
     * @param CatalogProductAttribute $attribute
     * @param CatalogAttributeSet $productTemplate
     */
    public function __construct(
        CatalogProductSetIndex $catalogProductSetIndex,
        CatalogProductSetEdit $catalogProductSetEdit,
        CatalogProductAttribute $attribute,
        CatalogAttributeSet $productTemplate
    ) {
        $this->catalogProductSetIndex = $catalogProductSetIndex;
        $this->catalogProductSetEdit = $catalogProductSetEdit;
        $this->attribute = $attribute;
        $this->productTemplate = $productTemplate;
    }

    /**
     * Move Attribute To AttributeSet Step.
     *
     * @return void
     */
    public function run()
    {
        $filterAttribute = ['set_name' => $this->productTemplate->getAttributeSetName()];
        $this->catalogProductSetIndex->open()->getGrid()->searchAndOpen($filterAttribute);

        $this->catalogProductSetEdit->getAttributeSetEditBlock()->moveAttribute(
            $this->attribute->getData(),
            'Product Details'
        );
    }
}
