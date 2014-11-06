<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module;

use Magento\Framework\DB\Adapter\AdapterInterface;

/*
 * Resource writer model for schema/data install/upgrade
 */
class ResourceWriter extends \Magento\Framework\Module\Resource
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
     * {@inheritdoc}
     */
    public function setDataVersion($resName, $version)
    {
        $data = array('code' => $resName, 'data_version' => $version);

        if ($this->getDbVersion($resName) || $this->getDataVersion($resName)) {
            self::$_dataVersions[$resName] = $version;
            $this->_getWriteAdapter()->update($this->getMainTable(), $data, array('code = ?' => $resName));
        } else {
            self::$_dataVersions[$resName] = $version;
            $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
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
