<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core config data resource model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Config_Data extends Mage_Core_Model_Resource_Db_Abstract
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
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Config_Data
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
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
     * @param Mage_Core_Model_Config_Value $object
     * @return Mage_Core_Model_Resource_Config_Data
     */
    protected function _checkUnique(Mage_Core_Model_Abstract $object)
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
    public function clearWebsiteData(Mage_Core_Model_Website $website)
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
