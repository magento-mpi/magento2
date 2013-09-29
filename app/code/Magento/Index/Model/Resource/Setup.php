<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Index Setup Model
 *
 * @category    Magento
 * @package     Magento_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Index\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @var \Magento\Index\Model\Indexer\ConfigInterface
     */
    protected $_indexerConfig;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Index\Model\Indexer\ConfigInterface $indexerConfig
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Index\Model\Indexer\ConfigInterface $indexerConfig,
        $resourceName,
        $moduleName = 'Magento_Index',
        $connectionName = ''
    ) {
        $this->_indexerConfig = $indexerConfig;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }


    /**
     * Apply Index module DB updates and sync indexes declaration
     *
     * @return void
     */
    public function applyUpdates()
    {
        parent::applyUpdates();
        $this->_syncIndexes();
    }

    /**
     * Sync indexes declarations in config and in DB
     *
     * @return \Magento\Index\Model\Resource\Setup
     */
    protected function _syncIndexes()
    {
        $connection = $this->getConnection();
        if (!$connection) {
            return $this;
        }
        $indexCodes = array();
        foreach (array_keys($this->_indexerConfig->getAll()) as $name) {
            $indexCodes[] = $name;
        }
        $table = $this->getTable('index_process');
        $select = $connection->select()->from($table, 'indexer_code');
        $existingIndexes = $connection->fetchCol($select);
        $delete = array_diff($existingIndexes, $indexCodes);
        $insert = array_diff($indexCodes, $existingIndexes);

        if (!empty($delete)) {
            $connection->delete($table, $connection->quoteInto('indexer_code IN (?)', $delete));
        }
        if (!empty($insert)) {
            $insertData = array();
            foreach ($insert as $code) {
                $insertData[] = array(
                    'indexer_code' => $code,
                    'status' => \Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX
                );
            }
            if (method_exists($connection, 'insertArray')) {
                $connection->insertArray($table, array('indexer_code', 'status'), $insertData);
            }
        }
    }
}
