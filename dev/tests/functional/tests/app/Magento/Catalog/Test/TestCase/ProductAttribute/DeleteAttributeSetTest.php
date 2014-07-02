<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\ProductAttribute;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;

/**
 * Test Creation for Delete Attribute Set (Product Template)
 *
 * Test Flow:
 * 1. Log in to Backend.
 * 2. Navigate to Stores > Attributes > Product Template.
 * 3. Open created Product Template according to attached ProductTemplate.php.
 * 4. Click 'Delete Attribute Set' button.
 * 5. Perform all assertions.
 *
 * @group Product_Attributes_(MX)
 * @ZephyrId MAGETWO-25473
 */
class DeleteAttributeSetTest extends Injectable
{
    /**
     * Catalog Product Set page
     *
     * @var CatalogProductSetIndex
     */
    protected $productSetIndex;

    /**
     * Catalog Product Set edit page
     *
     * @var CatalogProductSetEdit
     */
    protected $productSetEdit;

    /**
     * Inject data
     *
     * @param CatalogProductSetIndex $productSetIndex
     * @param CatalogProductSetEdit $productSetEdit
     * @return void
     */
    public function __inject(
        CatalogProductSetIndex $productSetIndex,
        CatalogProductSetEdit $productSetEdit
    ) {
        $this->productSetIndex = $productSetIndex;
        $this->productSetEdit = $productSetEdit;
    }

    /**
     * Run DeleteAttributeSet test
     *
     * @param FixtureFactory $fixtureFactory
     * @param CatalogAttributeSet $productTemplate
     * @param CatalogProductAttribute $productAttribute
     * @return array
     */
    public function test
    (
        FixtureFactory $fixtureFactory,
        CatalogAttributeSet $productTemplate,
        CatalogProductAttribute $productAttribute
    ) {
        //Precondition
        $productAttribute->persist();
        $productTemplate->persist();
        /** @var CatalogProductSimple $catalogProductSimple */
        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            [
                'dataSet' => 'default',
                'data' => [
                    'attribute_set_id' => ['attribute_set' => $productTemplate],
                ],
            ]
        );
        $product->persist();

        //Steps
        $filter = [
            'set_name' => $productTemplate->getAttributeSetName(),
        ];
        $this->productSetIndex->open();
        $this->productSetIndex->getGrid()->searchAndOpen($filter);
        $this->productSetEdit->getPageActions()->delete();

        return ['product' => $product];
    }
}
