<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mock Rollback worker for rolling back via ftp
 */
namespace Magento\Backup\Filesystem\Rollback;

class Fs extends AbstractRollback
{
    /**
     * Mock Files rollback implementation via ftp
     *
     * @see \Magento\Backup\Filesystem\Rollback\AbstractRollback::run()
     */
    public function run()
    {
        return;
    }
}