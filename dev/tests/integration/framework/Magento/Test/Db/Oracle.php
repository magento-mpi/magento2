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
 * Oracle platform database handler
 */
class Magento_Test_Db_Oracle extends Magento_Test_Db_DbAbstract
{
    /**
     * @var string
     */
    protected $_sid = '';

    /**
     * Oracle has different notation of the schema and host
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $schema
     * @param string $varPath
     * @param Magento_Shell $shell
     * @throws Magento_Exception
     */
    public function __construct($host, $user, $password, $schema, $varPath, Magento_Shell $shell)
    {
        if (!preg_match('/^[^\/]+\/[^\/]+$/', $schema)) {
            throw new Magento_Exception('Oracle DB schema must be specified in the following format: "<host>/<SID>".');
        }
        // the original host is ignored, but instead taken from schema notation
        list($host, $sid) = explode('/', $schema);
        $this->_sid = $sid;

        // user and DB name are equivalent in Oracle
        $schema = $user;

        parent::__construct($host, $user, $password, $schema, $varPath, $shell);
    }

    /**
     * Remove all DB objects
     *
     * @return bool
     */
    public function cleanup()
    {
        $script = dirname(__FILE__) . '/cleanup_database.oracle.sql';
        $this->_shell->execute(
            'sqlplus %s/%s@%s/%s @%s',
            array($this->_user, $this->_password, $this->_host, $this->_sid, $script)
        );
    }
}
