<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Downloadable\Test\Fixture\CatalogProductDownloadable;

/**
 * Class AssertDownloadableSamplesData
 * Assert that Sample block for downloadable product on front-end
 */
class AssertDownloadableSamplesData extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert Sample block for downloadable product on front-end
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductDownloadable $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, CatalogProductDownloadable $product)
    {
        $catalogProductView->init($product);
        $catalogProductView->open();
        $dBlock = $catalogProductView->getViewBlock()->getDownloadableSamplesBlock();
        $fields = $product->getData();
        //Steps:
        //1. Title for for sample block
        \PHPUnit_Framework_Assert::assertEquals(
            $dBlock->getDownloadableSamplesDataTitleForForLink(),
            $fields['downloadable_sample']['title'],
            'Title for for Samples block for downloadable product on front-end is not visible.'
        );
        foreach ($fields['downloadable_sample']['downloadable']['sample'] as $index => $sample) {
            $index++;
            //2. Titles for each sample
            //3. Samples are displaying according to Sort Order
            \PHPUnit_Framework_Assert::assertEquals(
                $dBlock->getDownloadableSamplesDataTitleForList($index),
                $sample['title'],
                'Title for Samples item block for downloadable product on front-end is not visible.'
            );
        }
    }

    /**
     * Text of Visible in downloadable assert for sample block
     *
     * @return string
     */
    public function toString()
    {
        return 'Sample block for downloadable product on front-end is visible.';
    }
}
