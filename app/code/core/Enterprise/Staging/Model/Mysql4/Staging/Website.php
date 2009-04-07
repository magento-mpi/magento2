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

class Enterprise_Staging_Model_Mysql4_Staging_Website extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * @var Enterprise_Staging_Model_Entry
     */
    protected $_entryPoint;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_website', 'staging_website_id');
    }

    /**
     * Before save processing
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $staging = $object->getStaging();
        if ($staging instanceof Enterprise_Staging_Model_Staging) {
            if ($staging->getId()) {
                $object->setStagingId($staging->getId());
            }
        }

        $password = trim($object->getMasterPassword());
        if ($password) {
             if(Mage::helper('core/string')->strlen($password)<6){
                Mage::throwException(Mage::helper('enterprise_staging')->__('Password must have at least 6 characters. Leading or trailing spaces will be ignored.'));
            }
            $object->setMasterPasswordHash($object->hashPassword($password));
        }

        if (!$object->getId()) {
            $value = Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss");
            $object->setCreatedAt($value);
        } else {
            $value = Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss");
            $object->setUpdatedAt($value);
        }

        parent::_beforeSave($object);

        return $this;
    }

    /**
     * After save resource model
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Staging_Model_Mysql4_Staging_Website
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsPureSave()) {
            $this->saveItems($object);

            $this->saveSlaveWebsite($object);

            $this->saveStagingStoreGroup($object);

            $this->saveStores($object);

            $this->saveSystemConfig($object);
        }

        parent::_afterSave($object);

        return $this;
    }

    /**
     * Save item resource model
     *
     * @param website collection $website
     * @return Enterprise_Staging_Model_Mysql4_Staging_Website
     */
    public function saveItems($website)
    {
        foreach ($website->getItemsCollection() as $item) {
            $item->save();
        }

        return $this;
    }

    /**
     * Save staging store group, create new
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Staging_Model_Mysql4_Staging_Website
     */
    public function saveStagingStoreGroup(Mage_Core_Model_Abstract $object)
    {

        if (!$object->getSlaveWebsiteId()) {
            return $this;
        }
        if ($object->getDefaultGroupId()) {
            return $this;
        }

        $stagingGroup   = Mage::getModel('enterprise_staging/staging_store_group');
        $stagingGroup->setData('staging_id',         $object->getStagingId());
        $stagingGroup->setData('staging_website_id', $object->getId());
        $stagingGroup->setData('master_website_id',  $object->getMasterWebsiteId());
        $stagingGroup->setData('slave_website_id',   $object->getSlaveWebsiteId());
        $stagingGroup->setData('root_category_id',   2); // TODO quick FIXME quick
        $stagingGroup->setData('name',               'Staging Store Group');
        $stagingGroup->save();

        $group   = Mage::getModel('core/store_group');
        $group->setData('website_id',           $object->getSlaveWebsiteId());
        $group->setData('root_category_id',     2); // TODO quick FIXME quick
        $group->setData('name',                 'Staging Store Group');
        $group->setIgnoreSyncStagingGroup(true);
        $group->save();

        $this->updateAttribute($object, 'default_group_id', $stagingGroup->getId());
        $object->setDefaultGroupId($stagingGroup->getId());
        $object->setSlaveGroupId($group->getId());

        return $this;
    }

    /**
     * save staging website resource model
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Staging_Model_Mysql4_Staging_Website
     */
    public function saveSlaveWebsite(Mage_Core_Model_Abstract $object)
    {
        $slaveWebsite   = Mage::getModel('core/website');
        $slaveWebsiteId = (int) $object->getSlaveWebsiteId();
        if ($slaveWebsiteId) {
            $slaveWebsite->load($slaveWebsiteId);
        }
        $slaveWebsite->setData('is_staging', 1);
        $slaveWebsite->setData('code', $object->getCode());
        $slaveWebsite->setData('name', $object->getName());
        $slaveWebsite->setData('master_login', $object->getMasterLogin());
        $slaveWebsite->setData('master_password', $object->getMasterPassword());
        $slaveWebsite->setData('master_password_hash', $object->getMasterPasswordHash());

        $this->_entryPoint = Mage::getModel('enterprise_staging/entry')->setWebsite($slaveWebsite)->save();

        $slaveWebsite->setIgnoreSyncStagingWebsite(true);
        $slaveWebsite->save();

        if (!$slaveWebsiteId) {
            $slaveWebsiteId = (int)$slaveWebsite->getId();
            $this->updateAttribute($object, 'slave_website_id', $slaveWebsiteId);
            $object->setSlaveWebsiteId($slaveWebsiteId);
            Mage::dispatchEvent('staging_website_create_after', array(
                'old_website_id' => $object->getMasterWebsiteId(), 'new_website_id' => $slaveWebsiteId)
            );
        }

        return $this;
    }

    /**
     * save store resource model
     *
     * @param store collection $object
     * @return Enterprise_Staging_Model_Mysql4_Staging_Website
     */
    public function saveStores($object)
    {
        foreach ($object->getStoresCollection() as $store) {
            $store->save();
        }

        return $this;
    }

    /**
     * save system config resource model
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Staging_Model_Mysql4_Staging_Website
     */
    public function saveSystemConfig($object)
    {
        if ($object->getEventCode() == 'create') {
            $unsecureBaseUrl = $object->getBaseUrl();
            $secureBaseUrl   = $object->getBaseSecureUrl();
            if ($this->_entryPoint && $this->_entryPoint->isAutomatic()) {
                $unsecureBaseUrl = $this->_entryPoint->getBaseUrl();
                $secureBaseUrl   = $this->_entryPoint->getBaseUrl(true);
            }

            $config = Mage::getModel('core/config_data');
            $path = 'web/unsecure/base_url';
            $config->setPath($path);
            $config->setScope('websites');
            $config->setScopeId($object->getSlaveWebsiteId());
            $config->setValue($unsecureBaseUrl);
            $config->save();

            $config = Mage::getModel('core/config_data');
            $path = 'web/secure/base_url';
            $config->setPath('web/secure/base_url');
            $config->setScope('websites');
            $config->setScopeId($object->getSlaveWebsiteId());
            $config->setValue($secureBaseUrl);
            $config->save();
        }

        return $this;
    }

    /**
     * Update specific attribute value (set new value back in given model)
     *
     * @param Enterprise_Staging_Model_Staging_Wbsite $website
     * @param string $attribute
     * @param mixed  $value
     *
     * @return Enterprise_Staging_Model_Mysql4_Staging_Website
     */
    public function updateAttribute($website, $attribute, $value)
    {
        $where = "staging_website_id=".(int)$website->getId();
        $this->_getWriteAdapter()
           ->update($this->getMainTable(), array($attribute => $value), $where);
       $website->setData($attribute, $value);
       return $this;
    }

    /**
     * Retrieve free (non-used) website code with code suffix (if specified in config)
     *
     * @param   string $code
     * @return  string
     */
    public function generateWebsiteCode($code)
    {
        return $this->getUnusedWebsiteCode($code) . $this->getWebsiteCodeSuffix();
    }

    /**
     * Retrieve free (non-used) website code
     *
     * @param   string $code
     * @return  string
     */
    public function getUnusedWebsiteCode($code)
    {
        if (empty($code)) {
            $code = '_';
        } elseif ($code == $this->getWebsiteCodeSuffix()) {
            $code = '_' . $this->getWebsiteCodeSuffix();
        }

        $website = $this->getWebsiteByCode($code);
        if ($website) {
            // retrieve code suffix for staging websites
            $websiteCodeSuffix = $this->getWebsiteCodeSuffix();

            $match = array();
            if (!preg_match('#^([0-9a-z_]+?)(_([0-9]+))?('.preg_quote($websiteCodeSuffix).')?$#i', $code, $match)) {
                return $this->getUnusedWebsiteCode('_');
            }
            $code = $match[1].(isset($match[3])?'_'.($match[3]+1):'_1').(isset($match[4])?$match[4]:'');
            return $this->getUnusedWebsiteCode($code);
        } else {
            return $code;
        }
    }

    /**
     * Retrieve website code sufix
     *
     * @return string
     */
    public function getWebsiteCodeSuffix()
    {
        return Mage::helper('enterprise_staging/website')->getWebsiteCodeSuffix();
    }

    public function getWebsiteByCode($code)
    {
        $select = $this->_getReadAdapter()->select()
           ->from($this->getTable('core/website'), 'website_id')
           ->where('code = ?', $code);

       return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Return saved store id
     * @param Enterprise_Staging_Model_Staging_Website $website
     * @return mixed
     */
    public function getStoreIds(Enterprise_Staging_Model_Staging_Website $website)
    {
        if (!$website->getId()) {
            return array();
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_staging/staging_store'), array('staging_store_id'))
            ->where('staging_website_id=?', $website->getId());
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * load wesbite resource model data by id
     *
     * @param Enterprise_Staging_Model_Staging_Website $website
     * @param int $id
     * @return Enterprise_Staging_Model_Mysql4_Staging_Website
     */
    public function loadBySlaveWebsiteId($website, $id)
    {
        $this->load($website, $id, 'slave_website_id');
        return $this;
    }

    /**
     * Sinc website resource model
     *
     * @param Mage_Core_Model_Abstract $object
     * @param Enterprise_Staging_Model_Staging_Website $website
     * @return Enterprise_Staging_Model_Mysql4_Staging_Website
     */
    public function syncWithWebsite($object, $website)
    {
        if ($website->getIgnoreSyncStagingWebsite()) {
            return $this;
        }

        $now = Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss");

        $object->setData('slave_website_id', $website->getId());
        $object->setData('slave_website_code', $website->getCode());

        $object->setData('code', $website->getCode());

        $object->setData('name', $website->getName());

        $stagingGroup = Mage::getModel('enterprise_staging/staging_store_group');
        /* @var $stagingGroup Enterprise_Staging_Model_Staging_Store_Group */
        $stagingGroup->loadBySlaveStoreGroupId($website->getDefaultGroupId());
        $object->setData('default_group_id', $stagingGroup->getId());

        $object->setData('is_default', $website->getIsDefault());

        $object->setData('sort_order', $website->getSortOrder());

        if (!$object->getId()) {
            $object->setData('visibility', Enterprise_Staging_Model_Staging_Config::VISIBILITY_NOT_ACCESSIBLE);
            $object->setData('master_login');
            $object->setData('master_password');
            $object->setData('master_password_hash');
        }

        if ($website->getApplyDate()) {
            $object->setData('apply_date', $website->getApplyDate());
            $object->setData('auto_apply_is_active', $website->getAutoApplyIsActive());
        }

        if ($website->getRollbackDate()) {
            $object->setData('rollback_date', $website->getRollbackDate());
            $object->setData('auto_rollback_is_active', $website->getAutoRollbackIsActive());
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