<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Db adapters factory
 */
class Tools_Migration_Acl_Db_Adapter_Factory
{
    /**
     * List of allowed adapter types
     * @var array
     */
    protected $_allowedAdapterTypes = array();

    public function __construct()
    {
        $this->_allowedAdapterTypes = array(
            'mssql',
            'mssqli',
            'mysql',
            'oracle',
        );
    }

    /**
     * @param string $type
     * @param array $config
     * @throws InvalidArgumentException
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter($type, array $config)
    {
        if (false == in_array($type, $this->_allowedAdapterTypes)) {
            throw new InvalidArgumentException('Invalid adapter type: ' . $type);
        }

        $dbAdapterClassName = null;
        switch ($type) {
            case 'mssql':
                $dbAdapterClassName = 'Varien_Db_Adapter_Pdo_Mssql';
                break;
            case 'oracle':
                $dbAdapterClassName = 'Varien_Db_Adapter_Oracle';
                break;
            case 'mysqli':
                $dbAdapterClassName = 'Varien_Db_Adapter_Mysqli';
                break;
            default:
                $dbAdapterClassName = 'Varien_Db_Adapter_Pdo_Mysql';
                break;
        }

        return new $dbAdapterClassName($config);
    }
}
