<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Staging_Model_Mysql4_Staging_Store extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_store', 'staging_store_id');
    }

    /**
     * Before save processing
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {

        $stagingWebsite = $object->getStagingWebsite();
        if ($stagingWebsite) {

            if ($stagingWebsite) {
                $object->setStagingWebsiteId($stagingWebsite->getId());
                $object->setMasterWebsiteId($stagingWebsite->getMasterWebsiteId());
                $object->setSlaveWebsiteId($stagingWebsite->getSlaveWebsiteId());

                $object->setStagingGroupId($stagingWebsite->getDefaultGroupId());

                $object->setSlaveGroupId($stagingWebsite->getSlaveGroupId());
            }
            $staging = $stagingWebsite->getStaging();
            if ($staging) {
                if ($staging->getId()) {
                    $object->setStagingId($staging->getId());
                }
            }
        } else {
            $staging = $object->getStaging();
            if ($staging) {
                if ($staging->getId()) {
                    $object->setStagingId($staging->getId());
                }
            }
        }

        if (!$object->getId()) {
            $value = Mage::getModel('core/date')->gmtDate();
            $object->setCreatedAt($value);

            $masterStore = $object->getMasterStore();
            if ($masterStore) {
                $object->setData('master_group_id', $masterStore->getGroupId());
            }
        } else {
            $value = Mage::getModel('core/date')->gmtDate();
            $object->setUpdatedAt($value);
        }

        parent::_beforeSave($object);

        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsPureSave()) {
            $this->saveItems($object);

            $this->saveSlaveStore($object);
        }
        parent::_afterSave($object);

        return $this;
    }

    public function saveItems($store)
    {
        foreach ($store->getItemsCollection() as $item) {
            $item->save();
        }

        return $this;
    }

    public function saveSlaveStore(Mage_Core_Model_Abstract $object)
    {
        $slaveStore   = Mage::getModel('core/store');
        $slaveStoreId = (int) $object->getSlaveStoreId();
        if ($slaveStoreId) {
            $slaveStore->load($slaveStoreId);
        }

        $slaveStore->setData('is_active', 1);
        $slaveStore->setData('is_staging', 1);
        $slaveStore->setData('website_id',  $object->getSlaveWebsiteId());
        $slaveStore->setData('group_id',    $object->getSlaveGroupId());
        $slaveStore->setData('code',        $object->getCode());
        $slaveStore->setData('name',        $object->getName());

        $slaveStore->setIgnoreSyncStagingStore(true);
        $slaveStore->save();

        if (!$slaveStoreId) {
            $slaveStoreId = (int) $slaveStore->getId();
            $this->updateAttribute($object, 'slave_store_id', $slaveStoreId);
            $object->setSlaveStoreId($slaveStoreId);
        }

        return $this;
    }

    /**
     * Update specific attribute value (set new value back in given model)
     *
     * @param   Enterprise_Staging_Model_Staging_Store $store
     * @param   string    $attribute
     * @param   mixed     $value
     * @return  Enterprise_Staging_Model_Mysql4_Staging_Store
     */
    public function updateAttribute($store, $attribute, $value)
    {
        $where = "staging_store_id=".(int)$store->getId();
        $this->_getWriteAdapter()
           ->update($this->getMainTable(), array($attribute => $value), $where);
        $store->setData($attribute, $value);
       return $this;
    }

    /**
     * Retrieve free (non-used) store code with code suffix (if specified in config)
     *
     * @param   string $code
     * @return  string
     */
    public function generateStoreCode($code)
    {
        return $this->getUnusedStoreCode($code). $this->getStoreCodeSuffix();
    }

    public function getUnusedStoreCode($code)
    {
        if (empty($code)) {
            $code = '_';
        } elseif ($code == $this->getStoreCodeSuffix()) {
            $code = '_' . $this->getStoreCodeSuffix();
        }

        $store = $this->getStoreIdByCode($code);
        if ($store) {
            $storeCodeSuffix = $this->getStoreCodeSuffix();

            $match = array();
            if (!preg_match('#^([0-9a-z_]+?)(_([0-9]+))?('.preg_quote($storeCodeSuffix).')?$#i', $code, $match)) {
                return $this->getUnusedStoreCode('_');
            }
            $code = $match[1].(isset($match[3])?'_'.($match[3]+1):'_1').(isset($match[4])?$match[4]:'');
            return $this->getUnusedStoreCode($code);
        } else {
            return $code;
        }
    }

    /**
     * Retrieve store code sufix for staging stores
     *
     * @return string
     */
    public function getStoreCodeSuffix()
    {
        return Mage::helper('enterprise_staging/store')->getStoreCodeSuffix();
    }

    /**
     * Retrieve store_id value for given store code
     *
     * @param   string $code
     * @return  int
     */
    public function getStoreIdByCode($code)
    {
        $select = $this->_getReadAdapter()->select()
           ->from($this->getTable('core/store'), 'store_id')
           ->where('code = ?', $code);

       return $this->_getReadAdapter()->fetchOne($select);
    }

    public function loadBySlaveStoreId($store, $id)
    {
        return parent::load($store, $id, 'slave_store_id');
    }

    public function syncWithStore($object, $store)
    {
        if ($store->getIgnoreSyncStagingStore()) {
            return $this;
        }

        $now = Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss");

        $object->setData('slave_store_id', $store->getId());
        $object->setData('slave_store_code', $store->getCode());
        $object->setData('code', $store->getCode());
        $object->setData('name', $store->getName());

        $stagingStore = Mage::getModel('enterprise_staging/staging_store');
        /* @var $stagingStore Enterprise_Staging_Model_Staging_Store */
        $stagingStore->loadBySlaveStoreId($store->getId());
        $object->setData('staging_group_id', $stagingStore->getStagingGroupId());
        $object->setData('master_group_id', $stagingStore->getMasterGroupId());

        $stagingGroup = Mage::getModel('enterprise_staging/staging_store_group');
        /* @var $stagingGroup Enterprise_Staging_Model_Staging_Store_Group */
        $stagingGroup->loadBySlaveStoreGroupId($store->getGroupId());
        $object->setData('staging_group_id', $stagingGroup->getId());

        $stagingWebsite = Mage::getModel('enterprise_staging/staging_website');
        /* @var $stagingWebsite Enterprise_Staging_Model_Staging_Website */
        $stagingWebsite->loadBySlaveWebsiteId($store->getWebsiteId());
        $object->setData('staging_website_id', $stagingWebsite->getId());
        $object->setData('staging_id', $stagingWebsite->getStagingId());
        $object->setData('master_website_id', $stagingWebsite->getMasterWebsiteId());

        $object->setData('is_default', $object->getIsDefault());

        $object->setData('is_active', $object->getIsActive());

        if (!$object->getId()) {
            $object->setData('visibility', Enterprise_Staging_Model_Staging_Config::VISIBILITY_NOT_ACCESSIBLE);
            $object->setData('master_login');
            $object->setData('master_password');
            $object->setData('master_password_hash');
        }

        if (!$object->getId()) {
            $object->setData('created_at', $now);
        } else {
            $object->setData('updated_at', $now);
        }

        $object->setIsPureSave(true);

        $object->save();

        return $this;
    }
}
