<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview\View;

class Changelog implements ChangelogInterface
{
    /**
     * Suffix for changelog table
     */
    const SUFFIX_NAME = 'cl';

    /**
     * Column name of changelog entity
     */
    const COLUMN_NAME = 'entity_id';

    /**
     * Database write connection
     *
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $write;

    /**
     * View Id identifier
     *
     * @var string
     */
    protected $viewId;

    /**
     * @param \Magento\App\Resource $resource
     * @param string $viewId
     */
    public function __construct(\Magento\App\Resource $resource, $viewId)
    {
        $this->write = $resource->getConnection('core_write');
        $this->viewId = $viewId;
        $this->checkConnection();
    }

    /**
     * Check DB connection
     *
     * @throws \Exception
     */
    protected function checkConnection()
    {
        if (!$this->write) {
            throw new \Exception('Write DB connection is not available');
        }
    }
    /**
     * Create changelog table
     *
     * @return boolean
     * @throws \Exception
     */
    public function create()
    {
        $changelogTableName = $this->write->getTableName($this->getName());
        if ($this->write->isTableExists($changelogTableName)) {
            throw new \Exception("Table {$changelogTableName} already exist");
        }

        $table = $this->write->newTable($changelogTableName)
            ->addColumn('version_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Version ID')
            ->addColumn($this->getColumnName(), \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ), 'Entity ID');
        $this->write->createTable($table);

        return true;
    }

    /**
     * Remove changelog table
     *
     * @return boolean
     * @throws \Exception
     */
    public function remove()
    {
        $changelogTableName = $this->write->getTableName($this->getName());
        if (!$this->write->isTableExists($changelogTableName)) {
            throw new \Exception("Table {$changelogTableName} does not exist");
        }

        $this->write->dropTable($changelogTableName);

        return true;
    }

    /**
     * Clear changelog table by version_id
     *
     * @param $versionId
     * @return boolean
     * @throws \Exception
     */
    public function clear($versionId)
    {
        $changelogTableName = $this->write->getTableName($this->getName());
        if (!$this->write->isTableExists($changelogTableName)) {
            throw new \Exception("Table {$changelogTableName} does not exist");
        }
        // TODO: Write clear functionality
    }

    /**
     * Retrieve entity ids by version_id
     *
     * @param $versionId
     * @return integer[]
     * @throws \Exception
     */
    public function getList($versionId)
    {
        $changelogTableName = $this->write->getTableName($this->getName());
        if (!$this->write->isTableExists($changelogTableName)) {
            throw new \Exception("Table {$changelogTableName} does not exist");
        }
        // TODO: Write getList functionality
    }

    /**
     * Get maximum version_id from changelog
     *
     * @return integer
     * @throws \Exception
     */
    public function getVersion()
    {
        $changelogTableName = $this->write->getTableName($this->getName());
        if (!$this->write->isTableExists($changelogTableName)) {
            throw new \Exception("Table {$changelogTableName} does not exist");
        }
        $select = $this->write->select()
            ->from($changelogTableName, new \Zend_Db_Expr('MAX(`version_id`)'));
        return (int)$this->write->fetchOne($select);
    }

    /**
     * Get changlog name
     *
     * @return string
     */
    public function getName()
    {
        return $this->viewId . '_' . self::SUFFIX_NAME;
    }

    /**
     * Get changlog entity column name
     *
     * @return string
     */
    public function getColumnName()
    {
        return self::COLUMN_NAME;
    }


}
