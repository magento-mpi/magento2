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
 * Class AssertStoreGroupSuccessDeleteAndBackupMessages
 * Assert that store group success delete and backup messages are present.
 */
class AssertStoreGroupSuccessDeleteAndBackupMessages extends AbstractConstraint
{
    /**
     * Success backup message
     */
    const SUCCESS_BACKUP_MESSAGE = 'The database was backed up.';

    /**
     * Success store group delete message
     */
    const SUCCESS_DELETE_MESSAGE = 'The store has been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success messages is displayed after deleting store group
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
        return 'Store group success delete and backup messages are present.';
    }
}
