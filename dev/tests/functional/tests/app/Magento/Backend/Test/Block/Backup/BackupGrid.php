<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Backup;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class BackupGrid
 * Backups grid block
 */
class BackupGrid extends GridInterface
{
    /**
     * Backup row selector in grid
     *
     * @var string
     */
    protected $backupRow = 'tr[data-role="row"]';

    /**
     * Check is backup row visible on grid
     *
     * @return bool
     */
    public function isBackupRowVisible()
    {
        return $this->_rootElement->find($this->backupRow)->isVisible();
    }
}
