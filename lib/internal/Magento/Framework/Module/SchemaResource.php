<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module;

use Magento\Framework\DB\Adapter\AdapterInterface;

/*
 * Resource model for Schema install/upgrade
 */
class SchemaResource extends Resource implements SchemaResourceInterface
{
    const MAIN_TABLE = 'core_resource';

    /**
     * Table prefix
     *
     * @var string
     */
    private $tablePrefix;

    /**
     * Class constructor
     *
     * @param AdapterInterface $adapter
     * @param string $tablePrefix
     */
    public function __construct(AdapterInterface $adapter, $tablePrefix)
    {
        $this->_connections['write'] = $adapter;
        $this->_connections['read'] = $adapter;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function setDbVersion($resName, $version)
    {
        $dbModuleInfo = array('code' => $resName, 'version' => $version);

        if ($this->getDbVersion($resName)) {
            self::$_versions[$resName] = $version;
            return $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                $dbModuleInfo,
                array('code = ?' => $resName)
            );
        } else {
            self::$_versions[$resName] = $version;
            return $this->_getWriteAdapter()->insert($this->getMainTable(), $dbModuleInfo);
        }
    }

    /**
     * Get name of the resources table.
     *
     * @return string
     */
    public function getMainTable()
    {
        return $this->_connections['read']->getTableName($this->tablePrefix . self::MAIN_TABLE);
    }
} 