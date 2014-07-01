<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertProductSuccessDeleteMessage
 * Assert that after deleting product success message "A total of 1 record(s) have been deleted."
 */
class AssertProductSuccessDeleteMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_MESSAGE = 'A total of %d record(s) have been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after deleting product success message "A total of 1 record(s) have been deleted."
     *
     * @param FixtureInterface $product
     * @param CatalogProductEdit $productPage
     * @return void
     */
    public function processAssert(FixtureInterface $product, CatalogProductEdit $productPage)
    {
        $countProducts = count($product);
        $actualMessage = $productPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $countProducts),
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'A total of 1 record(s) have been deleted.';
    }
}
