<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeNew;

/**
 * Class AssertUsedSuperAttributeImpossibilityDeleteMessages
 * Assert that it's impossible to delete configurable attribute that is used in created configurable product
 */
class AssertUsedSuperAttributeImpossibleDeleteMessages extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Impossible to delete message
     */
    const ERROR_DELETE_MESSAGE = 'This attribute is used in configurable products.';

    /**
     * Assert that it's impossible to delete configurable attribute that is used in created configurable product
     *
     * @param CatalogProductAttributeNew $newPage
     * @return void
     */
    public function processAssert(CatalogProductAttributeNew $newPage)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_DELETE_MESSAGE,
            $newPage->getMessagesBlock()->getErrorMessages(),
            'Wrong impossible to delete message is not displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Error delete message is present while deleting assigned configurable attribute.';
    }
}
