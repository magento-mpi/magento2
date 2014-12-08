<?php
/**
 * Cache state options provider
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Cache\State;

use Magento\Framework\Model\Resource\Db\AbstractDb;

class Options extends AbstractDb implements OptionsInterface
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('core_cache_option', 'code');
    }

    /**
     * Get all cache options
     *
     * @return array|false
     */
    public function getAllOptions()
    {
        $adapter = $this->_getReadAdapter();
        if ($adapter) {
            /**
             * Check if table exist (it protect upgrades. cache settings checked before upgrades)
             */
            if ($adapter->isTableExists($this->getMainTable())) {
                $select = $adapter->select()->from($this->getMainTable(), ['code', 'value']);
                return $adapter->fetchPairs($select);
            }
        }
        return false;
    }

    /**
     * Save all options to option table
     *
     * @param array $options
     * @return $this
     * @throws \Exception
     */
    public function saveAllOptions($options)
    {
        $adapter = $this->_getWriteAdapter();
        if (!$adapter) {
            return $this;
        }

        $data = [];
        foreach ($options as $code => $value) {
            $data[] = [$code, $value];
        }

        $adapter->beginTransaction();
        try {
            $this->_getWriteAdapter()->delete($this->getMainTable());
            if ($data) {
                $this->_getWriteAdapter()->insertArray($this->getMainTable(), ['code', 'value'], $data);
            }
        } catch (\Exception $e) {
            $adapter->rollback();
            throw $e;
        }
        $adapter->commit();

        return $this;
    }
}
