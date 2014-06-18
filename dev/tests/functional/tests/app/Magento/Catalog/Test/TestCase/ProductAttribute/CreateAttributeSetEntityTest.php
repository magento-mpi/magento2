<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\ProductAttribute;

use Mtf\TestCase\Injectable;
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
    protected $productSetAdd;

    /**
     * Catalog Product Set edit page
     *
     * @var CatalogProductSetEdit
     */
    protected $productSetEdit;

    /**
     * @param CatalogProductSetIndex $productSetIndex
     * @param CatalogProductSetAdd $productSetAdd
     * @param CatalogProductSetEdit $productSetEdit
     * @param CatalogProductAttribute $productAttribute
     * @return array
     */
    public function __inject(
        CatalogProductSetIndex $productSetIndex,
        CatalogProductSetAdd $productSetAdd,
        CatalogProductSetEdit $productSetEdit,
        CatalogProductAttribute $productAttribute
    ) {
        $this->productSetIndex = $productSetIndex;
        $this->productSetAdd = $productSetAdd;
        $this->productSetEdit = $productSetEdit;

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

        $this->productSetAdd->getAttributeSetForm()->fill($attributeSet);
        $this->productSetAdd->getPageActions()->save();
        $this->productSetEdit->getMain()->moveAttribute($productAttribute->getData(), 'Product Details');
        $this->productSetEdit->getPageActions()->save();
    }
}
