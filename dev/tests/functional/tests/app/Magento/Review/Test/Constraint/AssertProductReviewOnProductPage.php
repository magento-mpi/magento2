<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertProductReviewOnProductPage
 * Assert that product review available on product page
 */
class AssertProductReviewOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product review available on product page
     *
     * @param CatalogProductView $catalogProductView
     * @param ReviewInjectable $review
     * @param ReviewInjectable $reviewInitial
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        ReviewInjectable $review,
        ReviewInjectable $reviewInitial,
        Browser $browser
    ) {
        $errors = [];
        /** @var CatalogProductSimple $product */
        $product = $reviewInitial->getDataFieldConfig('entity_id')['source']->getEntity();
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        $reviewBlock = $catalogProductView->getCustomerReviewBlock();
        $catalogProductView->getViewBlock()->selectTab('Reviews');
        foreach ($review->getData() as $name => $value) {
            $reviewValue = $reviewBlock->getFieldValue($name);
            if (($reviewValue !== null) && (0 !== strcasecmp($value, trim($reviewValue)))) {
                $errors[] = '- field "' . $name . '" is not equals submitted one, error value "' . $value . '"';
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            'The Review contains the following errors:' . PHP_EOL . implode(PHP_EOL, $errors)
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product review is displayed correct.';
    }
}
