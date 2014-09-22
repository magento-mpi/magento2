<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Resource;

use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Module\ResourceInterface;

/**
 * Resource Resource Model
 */
class Resource implements ResourceInterface
{
    const MAIN_TABLE = 'core_resource';

    /**
     * Database versions
     *
     * @var array
     */
    protected static $versions = null;

    /**
     * Table prefix
     * @var string
     */
    protected $tablePrefix;

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Set table prefix
     *
     * @param string $tablePrefix
     * @return void
     */
    public function setTablePrefix($tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
    }

    protected function loadVersionDb()
    {
        self::$versions = array();
        // Db version column always exists

        if ($this->adapter->isTableExists($this->getMainTable())) {
            $select = $this->adapter->select()->from($this->getMainTable());
            $sql = new Sql($this->adapter);
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $this->adapter->query($selectString);
            if ($results instanceof ResultSet\ResultSetInterface && $results->count()) {
                foreach ($results as $row) {
                    self::$versions[$row['code']] = $row['version'];
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDbVersion($resName)
    {
        if (!$this->adapter) {
            return false;
        }
        $this->loadVersionDb();
        return isset(self::$versions[$resName]) ? self::$versions[$resName] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function setDbVersion($resName, $version)
    {
        $dbModuleInfo = array('code' => $resName, 'version' => $version);

        if ($this->getDbVersion($resName)) {
            self::$versions[$resName] = $version;
            return $this->adapter->update(
                $this->getMainTable(),
                $dbModuleInfo,
                array('code = ?' => $resName)
            );
        } else {
            self::$versions[$resName] = $version;
            return $this->adapter->insert($this->getMainTable(), $dbModuleInfo);
        }
    }

    protected function getMainTable()
    {
        return $this->adapter->getTableName($this->tablePrefix . self::MAIN_TABLE);
    }
}
