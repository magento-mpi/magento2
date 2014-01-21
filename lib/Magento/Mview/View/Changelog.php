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
    const NAME_SUFFIX = 'cl';

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
     */
    public function __construct(\Magento\App\Resource $resource)
    {
        $this->write = $resource->getConnection('core_write');
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
     * Drop changelog table
     *
     * @return boolean
     * @throws \Exception
     */
    public function drop()
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
     * @param integer $versionId
     * @return boolean
     * @throws \Exception
     */
    public function clear($versionId)
    {
        $changelogTableName = $this->write->getTableName($this->getName());
        if (!$this->write->isTableExists($changelogTableName)) {
            throw new \Exception("Table {$changelogTableName} does not exist");
        }

        $this->write->delete($changelogTableName, array('version_id <= ?' => (int)$versionId));

        return true;
    }

    /**
     * Retrieve entity ids by range [$fromVersionId..$toVersionId]
     *
     * @param integer $fromVersionId
     * @param integer $toVersionId
     * @return integer[]
     * @throws \Exception
     */
    public function getList($fromVersionId, $toVersionId)
    {
        $changelogTableName = $this->write->getTableName($this->getName());
        if (!$this->write->isTableExists($changelogTableName)) {
            throw new \Exception("Table {$changelogTableName} does not exist");
        }

        $select = $this->write->select()
            ->distinct(true)
            ->from($changelogTableName, array($this->getColumnName()))
            ->where('version_id > ?', (int)$fromVersionId)
            ->where('version_id <= ?', (int)$toVersionId);

        return $this->write->fetchCol($select);
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
     * Build a changelog name by concatenating view identifier and changelog name suffix.
     *
     * @throws \Exception
     * @return string
     */
    public function getName()
    {
        if (strlen($this->viewId) == 0) {
            throw new \Exception("View's identifier is not set");
        }
        return $this->viewId . '_' . self::NAME_SUFFIX;
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

    /**
     * Set view's identifier
     *
     * @param string $viewId
     */
    public function setViewId($viewId)
    {
        $this->viewId = $viewId;
    }

    /**
     * @return string
     */
    public function getViewId()
    {
        return $this->viewId;
    }
}
