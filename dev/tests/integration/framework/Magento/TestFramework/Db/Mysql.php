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
 * MySQL platform database handler
 */
class Magento_TestFramework_Db_Mysql extends Magento_TestFramework_Db_DbAbstract
{
    /**
     * Remove all DB objects
     */
    public function cleanup()
    {
        $script = $this->_varPath . DIRECTORY_SEPARATOR . 'drop_create_database.sql';
        $this->_createScript($script, "DROP DATABASE `{$this->_schema}`; CREATE DATABASE `{$this->_schema}`");
        $this->_shell->execute(
            'mysql --protocol=TCP --host=%s --user=%s --password=%s %s < %s',
            array($this->_host, $this->_user, $this->_password, $this->_schema, $script)
        );
    }
}
