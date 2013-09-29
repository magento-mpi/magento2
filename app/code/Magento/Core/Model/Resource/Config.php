<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core Resource Resource Model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource;

class Config extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('core_config_data', 'config_id');
    }

    /**
     * Save config value
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return \Magento\Core\Model\Resource\Config
     */
    public function saveConfig($path, $value, $scope, $scopeId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $select = $writeAdapter->select()
            ->from($this->getMainTable())
            ->where('path = ?', $path)
            ->where('scope = ?', $scope)
            ->where('scope_id = ?', $scopeId);
        $row = $writeAdapter->fetchRow($select);

        $newData = array(
            'scope'     => $scope,
            'scope_id'  => $scopeId,
            'path'      => $path,
            'value'     => $value
        );

        if ($row) {
            $whereCondition = array($this->getIdFieldName() . '=?' => $row[$this->getIdFieldName()]);
            $writeAdapter->update($this->getMainTable(), $newData, $whereCondition);
        } else {
            $writeAdapter->insert($this->getMainTable(), $newData);
        }
        return $this;
    }

    /**
     * Delete config value
     *
     * @param string $path
     * @param string $scope
     * @param int $scopeId
     * @return \Magento\Core\Model\Resource\Config
     */
    public function deleteConfig($path, $scope, $scopeId)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getMainTable(), array(
            $adapter->quoteInto('path = ?', $path),
            $adapter->quoteInto('scope = ?', $scope),
            $adapter->quoteInto('scope_id = ?', $scopeId)
        ));
        return $this;
    }
}
