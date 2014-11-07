<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module;

use \Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Resource Model
 */
class Resource extends \Magento\Framework\Module\Resource
{
    const MAIN_TABLE = 'core_resource';

    /**
     * Table prefix
     *
     * @var string
     */
    private $_tablePrefix;

    /**
     * Class constructor
     *
     * @param AdapterInterface $adapter
     * @param string $tablePrefix
     */
    public function __construct(AdapterInterface $adapter, $tablePrefix)
    {
        $this->_connections['read'] = $adapter;
        $this->_connections['write'] = $adapter;
        $this->_tablePrefix = $tablePrefix;
    }
    /**
     * Get name of main table
     *
     * @return string
     */
    public function getMainTable() {
        return $this->_getReadAdapter()->getTableName($this->_tablePrefix . self::MAIN_TABLE);
    }
}
