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
 * Class AssertSearchTermSuccessMassDeleteMessage
 * Assert that success message is displayed after search term mass deleted
 */
class AssertSearchTermSuccessMassDeleteMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_MESSAGE = 'Total of %d record(s) were deleted';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that success message is displayed after search term mass deleted
     *
     * @param array $searchTerms
     * @param CatalogSearchIndex $indexPage
     * @return void
     */
    public function processAssert(array $searchTerms, CatalogSearchIndex $indexPage)
    {
        $actualMessage = $indexPage->getMessagesBlock()->getSuccessMessages();
        $successMessages = sprintf(self::SUCCESS_MESSAGE, count($searchTerms));
        \PHPUnit_Framework_Assert::assertEquals(
            $successMessages,
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
