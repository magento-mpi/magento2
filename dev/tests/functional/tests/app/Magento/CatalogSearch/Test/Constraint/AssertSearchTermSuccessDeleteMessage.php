<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogSearch\Test\Page\Adminhtml\CatalogSearchIndex;

/**
 * Class AssertSearchTermSuccessDeleteMessage
 * Assert that success message is displayed after search term deleted
 */
class AssertSearchTermSuccessDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Text value to be checked
     */
    const SUCCESS_DELETE_MESSAGE = 'You deleted the search.';

    /**
     * Assert that success message is displayed after search term deleted
     *
     * @param CatalogSearchIndex $indexPage
     * @return void
     */
    public function processAssert(CatalogSearchIndex $indexPage)
    {
        $actualMessage = $indexPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Search term success delete message is present.';
    }
}
