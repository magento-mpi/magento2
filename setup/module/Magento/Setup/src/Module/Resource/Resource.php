<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module\Resource;

use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet;
use Magento\Setup\Framework\DB\Adapter\AdapterInterface;
use Magento\Setup\Module\ResourceInterface;

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
     * DB adapter object
     *
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Table prefix
     *
     * @var string
     */
    private $tablePrefix;

    /**
     * Constructor
     *
     * @param AdapterInterface $adapter
     * @param string $tablePrefix
     */
    public function __construct(AdapterInterface $adapter, $tablePrefix)
    {
        $this->adapter = $adapter;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * Load schema/db version
     *
     * @return $this
     */
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

    /**
     * Get name of the resources table.
     *
     * @return string
     */
    protected function getMainTable()
    {
        return $this->adapter->getTableName($this->tablePrefix . self::MAIN_TABLE);
    }
}
