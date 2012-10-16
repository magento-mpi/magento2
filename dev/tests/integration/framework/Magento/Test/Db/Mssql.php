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
     * SQL client command line tool
     *
     * @var string
     */
    protected $_sqlClientCmd;

    /**
     * Validate whether a command exists in the environment or not
     *
     * @param string $command
     * @return bool
     */
    protected function _validateCommand($command)
    {
        try {
            $this->_shell->execute($command);
            return true;
        } catch (Magento_Exception $e) {
            return false;
        }
    }

    /**
     * Determine and retrieve the SQL client command line utility name
     *
     * @return string
     * @throws Magento_Exception
     */
    protected function _getSqlClientCmd()
    {
        if (!$this->_sqlClientCmd) {
            if ($this->_validateCommand('sqlcmd -?')) {
                $this->_sqlClientCmd = 'sqlcmd';
            } else if ($this->_validateCommand('tsql -C')) {
                $this->_sqlClientCmd = 'tsql';
            } else {
                throw new Magento_Exception('Neither command line utility "sqlcmd" nor "tsql" is installed.');
            }
        }
        return $this->_sqlClientCmd;
    }

    /**
     * Remove all DB objects
     */
    public function cleanup()
    {
        $cleanupFile = $this->_varPath . DIRECTORY_SEPARATOR . 'mssql_cleanup_database.sql';
        $script = "USE [master]
GO
ALTER DATABASE [{$this->_schema}] SET SINGLE_USER WITH ROLLBACK IMMEDIATE
GO
DROP DATABASE [{$this->_schema}]
GO
CREATE DATABASE [{$this->_schema}]
GO
USE [{$this->_schema}]
GO
exit
";
        $this->_createScript($cleanupFile, $script);
        $this->_shell->execute(
            $this->_getSqlClientCmd() . ' -S %s -U %s -P %s < %s',
            array($this->_host, $this->_user, $this->_password, $cleanupFile)
        );
    }
}
