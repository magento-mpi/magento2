<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Adminhtml\BackupIndex;

/**
 * Class AssertBackupInGrid
 * Assert that created backup can be found in Backups grid
 */
class AssertBackupInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created backup can be found in Backups grid
     *
     * @param BackupIndex $backupIndex
     * @return void
     */
    public function processAssert(BackupIndex $backupIndex)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $backupIndex->open()->getBackupGrid()->isBackupRowVisible(),
            'Backup is not present in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Backup is present in grid.';
    }
}
