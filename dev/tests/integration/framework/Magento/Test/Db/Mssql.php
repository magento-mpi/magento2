<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
