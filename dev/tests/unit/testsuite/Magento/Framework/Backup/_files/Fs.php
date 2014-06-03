<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mock rollback worker for rolling back via local filesystem
 */
namespace Magento\Framework\Backup\Filesystem\Rollback;

class Fs extends \Magento\Framework\Backup\Filesystem\Rollback\AbstractRollback
{
    /**
     * Mock Files rollback implementation via local filesystem
     *
     * @see \Magento\Framework\Backup\Filesystem\Rollback\AbstractRollback::run()
     */
    public function run()
    {
        return;
    }
}
