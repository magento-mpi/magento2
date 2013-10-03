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
 * Core config data resource model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource\Config;

class Data extends \Magento\Core\Model\Resource\Db\AbstractDb
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
     * Convert array to comma separated value
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\Core\Model\Resource\Config\Data
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        if (!$object->getId()) {
            $this->_checkUnique($object);
        }

        if (is_array($object->getValue())) {
            $object->setValue(join(',', $object->getValue()));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Validate unique configuration data before save
     * Set id to object if exists configuration instead of throw exception
     *
     * @param \Magento\Core\Model\Config\Value $object
     * @return \Magento\Core\Model\Resource\Config\Data
     */
    protected function _checkUnique(\Magento\Core\Model\AbstractModel $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array($this->getIdFieldName()))
            ->where('scope = :scope')
            ->where('scope_id = :scope_id')
            ->where('path = :path');
        $bind   = array(
            'scope'     => $object->getScope(),
            'scope_id'  => $object->getScopeId(),
            'path'      => $object->getPath()
        );

        $configId = $this->_getReadAdapter()->fetchOne($select, $bind);
        if ($configId) {
            $object->setId($configId);
        }

        return $this;
    }

    /**
     * Clear website data
     *
     * @param $website
     */
    public function clearWebsiteData(\Magento\Core\Model\Website $website)
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(), array('scope = ?' => 'websites', 'scope_id' => $website->getId())
        );
        $this->clearStoreData($website->getStoreIds());
    }

    /**
     * Cleare store data
     *
     * @param array $storeIds
     */
    public function clearStoreData(array $storeIds)
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(), array('scope = ?' => 'stores', 'scope_id IN (?)' => $storeIds)
        );
    }
}
