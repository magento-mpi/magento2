<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductTemplateInGrid
 * Checks present product template in Product Templates grid
 */
class AssertProductTemplateInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that new product template displays in Product Templates grid
     *
     * @param CatalogProductSetIndex $productSet
     * @param CatalogAttributeSet $attributeSet
     * @return void
     */
    public function processAssert(CatalogProductSetIndex $productSet, CatalogAttributeSet $attributeSet)
    {
        $filterAttribute = [
            'set_name' => $attributeSet->getAttributeSetName(),
        ];

        $productSet->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $productSet->getGrid()->isRowVisible($filterAttribute),
            'Attribute Set \'' . $filterAttribute['set_name'] . '\' is absent in Product Template grid.'
        );
    }

    /**
     * Text present new product template in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Product template is present in Product Templates grid';
    }
}
