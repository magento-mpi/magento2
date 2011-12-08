<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging action resource
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Staging_Action extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_staging_action', 'action_id');
    }

    /**
     * Before save processing
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Staging_Model_Resource_Staging_Action
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $staging = $object->getStaging();
        if ($staging instanceof Enterprise_Staging_Model_Staging) {
            if ($staging->getId()) {
                $object->setStagingId($staging->getId());
                $object->setStagingWebsiteId($staging->getStagingWebsiteId());
                $object->setMasterWebsiteId($staging->getMasterWebsiteId());
            }
        }

        if (!$object->getId() && !$object->getCreatedAt()) {
            $value = $this->formatDate(time());
            $object->setCreatedAt($value);
        }
        if ($object->getId()) {
            $value = $this->formatDate(time());
            $object->setUpdatedAt($value);
        }

        parent::_beforeSave($object);

        return $this;
    }

    /**
     * Action after delete
     * Need to delete all backup tables also
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Staging_Model_Resource_Staging_Action
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        if ($object->getIsDeleteTables() === true) {
            $stagingTablePrefix = $object->getStagingTablePrefix();
            $tables = Mage::getResourceHelper('Enterprise_Staging')->getTableNamesByPrefix($stagingTablePrefix);
            $connection = $this->_getWriteAdapter();

            foreach ($tables AS $table) {
                $connection->disableTableKeys($table);
                $connection->dropTable($table);
            }

        }
        return $this;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $stagingTablePrefix
     * @return unknown
     */
    public function getBackupTables($stagingTablePrefix)
    {
        return Mage::getResourceHelper('Enterprise_Staging')->getTableNamesByPrefix($stagingTablePrefix);
    }
}
