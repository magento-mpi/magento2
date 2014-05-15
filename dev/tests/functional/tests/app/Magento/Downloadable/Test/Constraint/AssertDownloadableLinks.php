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
 * Class AssertDownloadableLinks
 * Assert that Link block for downloadable product on front-end
 */
class AssertDownloadableLinks extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert Link block for downloadable product on front-end
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductDownloadable $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, CatalogProductDownloadable $product)
    {
        $catalogProductView->init($product);
        $catalogProductView->open();
        $dBlock = $catalogProductView->getViewBlock()->getDownloadableLinksBlock();
        $fields = $product->getData();
        //Steps:
        //1. Title for for Link block
        \PHPUnit_Framework_Assert::assertEquals(
            $dBlock->getDownloadableLinksDataTitleForForLink(),
            $fields['downloadable_links']['title'],
            'Title for for Link block for downloadable product on front-end is not visible.'
        );

        if (isset($fields['downloadable_links'])) {
            foreach ($fields['downloadable_links']['downloadable']['link'] as $index => $link) {
                $index++;
                //2. Titles for each links
                //6. Links are displaying according to Sort Order
                \PHPUnit_Framework_Assert::assertEquals(
                    $dBlock->getDownloadableLinksDataTitleForList($index),
                    $link['title'],
                    'Link item title for downloadable product on front-end is not visible.'
                );

                //3. If Links can be Purchase Separately, check-nob is presented near each link
                //4. If Links CANNOT be Purchase Separately, check-nob is not presented near each link
                if ($fields['downloadable_links']['links_purchased_separately'] == "Yes") {
                    \PHPUnit_Framework_Assert::assertTrue(
                        $dBlock->getDownloadableLinksDataCheckboxForList($index),
                        'Checkbox item link block for downloadable product on front-end is not visible.'
                    );
                    //5. Price is equals passed according to fixture
                    $link['price'] = sprintf('$%1.2f', $link['price']);
                    \PHPUnit_Framework_Assert::assertEquals(
                        $dBlock->getDownloadableLinksDataPriceForList($index),
                        $link['price'],
                        'Link item title for downloadable product on front-end is not visible.'
                    );
                } elseif ($fields['downloadable_links']['links_purchased_separately'] == "No") {
                    \PHPUnit_Framework_Assert::assertFalse(
                        $dBlock->getDownloadableLinksDataCheckboxForList($index),
                        'Checkbox item link block for downloadable product on front-end is visible.'
                    );
                }
            }
        }
    }

    /**
     * Text of Visible in downloadable assert for link block
     *
     * @return string
     */
    public function toString()
    {
        return 'Link block for downloadable product on front-end is visible.';
    }
}
