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
 * Class AssertSearchTermSuccessSaveMessage
 * Assert that success message is displayed after search term save
 */
class AssertSearchTermSuccessSaveMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_MESSAGE = 'You saved the search term.';

    /* tags */
     const SEVERITY = 'high';
     /* end tags */

    /**
     * Assert that success message is displayed after search term save
     *
     * @param CatalogSearchIndex $indexPage
     * @return void
     */
    public function processAssert(CatalogSearchIndex $indexPage)
    {
        $actualMessage = $indexPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . PHP_EOL . "Expected: " . self::SUCCESS_MESSAGE
            . PHP_EOL . "Actual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Search term success save message is present.';
    }
}
