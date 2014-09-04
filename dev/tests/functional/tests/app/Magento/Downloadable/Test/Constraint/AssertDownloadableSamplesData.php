<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Downloadable\Test\Fixture\CatalogProductDownloadable;

/**
 * Class AssertDownloadableSamplesData
 *
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
     * @param CatalogProductView $productView
     * @param CatalogProductDownloadable $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $productView,
        CatalogProductDownloadable $product,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $sampleBlock = $productView->getDownloadableViewBlock()->getDownloadableSamplesBlock();
        $fields = $product->getData();

        // Title for for sample block
        \PHPUnit_Framework_Assert::assertEquals(
            $sampleBlock->getTitleForSampleBlock(),
            $fields['downloadable_sample']['title'],
            'Title for for Samples block for downloadable product on front-end is not correct.'
        );

        $this->sortDownloadableArray($fields['downloadable_sample']['downloadable']['sample']);

        foreach ($fields['downloadable_sample']['downloadable']['sample'] as $index => $sample) {
            // Titles for each sample
            // Samples are displaying according to Sort Order
            \PHPUnit_Framework_Assert::assertEquals(
                $sampleBlock->getItemTitle(++$index),
                $sample['title'],
                'Sample item ' . $index . ' with title "' . $sample['title'] . '" is not visible.'
            );
        }
    }

    /**
     * Sort downloadable sample array
     *
     * @param array $fields
     * @return array
     */
    protected function sortDownloadableArray(&$fields)
    {
        usort(
            $fields,
            function ($a, $b) {
                if ($a['sort_order'] == $b['sort_order']) {
                    return 0;
                }
                return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
            }
        );
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
