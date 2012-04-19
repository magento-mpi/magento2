<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MSSQL platform database handler
 */
class Magento_Test_Db_Mssql extends Magento_Test_Db_DbAbstract
{
    /**
     * Command line connection script name
     *
     * @var string
     */
    protected $_program = null;

    /**
     * Getter for MSSQL external command name
     *
     * @return string
     */
    public function getExternalProgram()
    {
        if (!$this->_program) {
            if ($this->_exec('sqlcmd -?')) {
                $this->_program = 'sqlcmd';
            } elseif ($this->_exec('tsql -C')) {
                $this->_program = 'tsql';
            } else {
                throw new Exception('Command lime utility (tsql or sqlcmd) is not installed.');
            }
        }
        return $this->_program;
    }

    /**
     * Create empty DB backup before installation
     *
     * @return bool
     */
    public function verifyEmptyDatabase()
    {
        return parent::verifyEmptyDatabase() && $this->createBackup('empty_db');
    }

    /**
     * Remove all DB objects
     *
     * @return bool
     */
    public function cleanup()
    {
        return $this->restoreBackup('empty_db');
    }

    /**
     * Create database backup
     *
     * @param string $name
     * @return bool
     */
    public function createBackup($name)
    {
        $backupSqlFile = $this->_varPath . '/mssql_backup_script.sql';
        $script = "BACKUP DATABASE [{$this->_schema}] TO DISK=N'{$this->_getBackupFile($name)}'"
            . " WITH NOFORMAT, NOINIT, SKIP, NOREWIND, NOUNLOAD\nGO\nexit\n"
        ;
        $this->_createScript($backupSqlFile, $script);
        $cmd = sprintf($this->getExternalProgram() . ' -S %s -U %s -P %s < %s',
            escapeshellarg($this->_host), escapeshellarg($this->_user),
            escapeshellarg($this->_password), escapeshellarg($backupSqlFile)
        );
        return $this->_exec($cmd);
    }

    /**
     * Restore database from backup
     *
     * @param string $name
     * @return bool
     */
    public function restoreBackup($name)
    {
        $backupSqlFile = $this->_varPath . '/mssql_restore_script.sql';
        $script = "USE [master]
GO
ALTER DATABASE [{$this->_schema}] SET SINGLE_USER WITH ROLLBACK IMMEDIATE
GO
RESTORE DATABASE [{$this->_schema}] FROM DISK=N'{$this->_getBackupFile($name)}' WITH REPLACE
GO
ALTER DATABASE [{$this->_schema}] SET MULTI_USER WITH NO_WAIT
GO
exit
";
        $this->_createScript($backupSqlFile, $script);
        $cmd = sprintf($this->getExternalProgram() . ' -S %s -U %s -P %s < %s',
            escapeshellarg($this->_host), escapeshellarg($this->_user),
            escapeshellarg($this->_password), escapeshellarg($backupSqlFile)
        );
        return $this->_exec($cmd);
    }

    /**
     * Get backup file name based on backup name
     *
     * @param  $name
     * @return string
     */
    protected function _getBackupFile($name)
    {
        return $this->_schema . '_' . $name . '.bak';
    }
}
