<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;

/**
 * Class AssertProductTemplateNotInGrid
 * Check Product Template absence on grid
 */
class AssertProductTemplateNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that new product template displays in Product Templates grid
     *
     * @param CatalogProductSetIndex $productSetPage
     * @param CatalogAttributeSet $productTemplate
     * @return void
     */
    public function processAssert(CatalogProductSetIndex $productSetPage, CatalogAttributeSet $productTemplate)
    {
        $filterAttributeSet = [
            'set_name' => $productTemplate->getAttributeSetName(),
        ];

        $productSetPage->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $productSetPage->getGrid()->isRowVisible($filterAttributeSet),
            'Attribute Set \'' . $filterAttributeSet['set_name'] . '\' is present in Product Template grid.'
        );
    }

    /**
     * Text absent new product template in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Product template is absent in Product Templates grid';
    }
}
