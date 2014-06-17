<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\ProductAttribute;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetAdd;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;

/**
 * Test Creation for CreateAttributeSetEntity
 *
 * Test Flow:
 * 1. Log in to Backend.
 * 2. Navigate to Stores > Attributes > Product Template.
 * 3. Start to create new Product Template.
 * 4. Fill out fields data according to data set.
 * 5. Add created Product Attribute to Product Template.
 * 6. Save new Product Template.
 * 7. Verify created Product Template.
 *
 * @group Product_Attributes_(CS)
 * @ZephyrId MAGETWO-25104
 */
class CreateAttributeSetEntityTest extends Injectable
{
    /**
     * Catalog Product Set page
     *
     * @var CatalogProductSetIndex
     */
    protected $productSetIndex;

    /**
     * Catalog Product Set add page
     *
     * @var CatalogProductSetAdd
     */
    protected $setAdd;

    /**
     * Catalog Product Set edit page
     *
     * @var CatalogProductSetEdit
     */
    protected $productSetEdit;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductSetIndex $productSetIndex
     * @param CatalogProductSetAdd $setAdd
     * @param CatalogProductSetEdit $productSetEdit
     * @return array
     */
    public function __inject(
        FixtureFactory $fixtureFactory,
        CatalogProductSetIndex $productSetIndex,
        CatalogProductSetAdd $setAdd,
        CatalogProductSetEdit $productSetEdit
    ) {
        $this->productSetIndex = $productSetIndex;
        $this->setAdd = $setAdd;
        $this->productSetEdit = $productSetEdit;

        $productAttribute = $fixtureFactory->createByCode(
            'catalogProductAttribute',
            ['dataSet' => 'attribute_type_text_field']
        );
        $productAttribute->persist();

        return [
            'productAttribute' => $productAttribute
        ];
    }

    /**
     * Run CreateAttributeSetEntity test
     *
     * @param CatalogAttributeSet $attributeSet
     * @param CatalogProductAttribute $productAttribute
     * @return void
     */
    public function testCreateAttributeSet(
        CatalogAttributeSet $attributeSet,
        CatalogProductAttribute $productAttribute
    ) {
        //Steps
        $this->productSetIndex->open();
        $this->productSetIndex->getPageActionsBlock()->addNew();

        $this->setAdd->getAttributeSetForm()->fill($attributeSet);
        $this->setAdd->getPageActions()->save();
        $this->productSetEdit->getMain()->moveAttribute($productAttribute->getData(), 'Product Details');
        $this->productSetEdit->getPageActions()->save();
    }
}
