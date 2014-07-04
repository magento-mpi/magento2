<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertProductCompareSuccessAddedMessage
 */
class AssertProductCompareSuccessAddedMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You added product %s to the comparison list.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';


    /**
     * Assert success message is presented on page.
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        $successMessage = sprintf(self::SUCCESS_MESSAGE, $product->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $successMessage,
            $catalogProductView->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $catalogProductView->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Text success present save message
     *
     * @return string
     */
    public function toString()
    {
        return 'This product adds to compare product list.';
    }
}
