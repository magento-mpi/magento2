<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Adminhtml\StoreIndex;

/**
 * Class AssertWebsiteSuccessDeleteAndBackupMessages
 * Assert that after website delete successful messages appears
 */
class AssertWebsiteSuccessDeleteAndBackupMessages extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success backup message
     */
    const SUCCESS_BACKUP_MESSAGE = 'The database was backed up.';

    /**
     * Success website delete message
     */
    const SUCCESS_DELETE_MESSAGE = 'The website has been deleted.';

    /**
     * Assert that success messages is displayed after deleting website
     *
     * @param StoreIndex $storeIndex
     * @return void
     */
    public function processAssert(StoreIndex $storeIndex)
    {
        $actualMessages = $storeIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array(self::SUCCESS_BACKUP_MESSAGE, $actualMessages) &&
            in_array(self::SUCCESS_DELETE_MESSAGE, $actualMessages),
            'Wrong success messages are displayed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Website success delete and backup messages are present.';
    }
}
