<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;

/**
 * Class AssertProductTemplateSuccessDeleteMessage
 * Check Product Templates success delete message
 */
class AssertProductTemplateSuccessDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Text value to be checked
     */
    const SUCCESS_DELETE_MESSAGE = 'The attribute set has been removed.';

    /**
     * Assert that after deleting product template success delete message appears
     *
     * @param CatalogProductSetIndex $productSetIndex
     * @return void
     */
    public function processAssert(CatalogProductSetIndex $productSetIndex)
    {
        $actualMessage = $productSetIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_DELETE_MESSAGE
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
        return 'Product Templates success delete message is present.';
    }
}
