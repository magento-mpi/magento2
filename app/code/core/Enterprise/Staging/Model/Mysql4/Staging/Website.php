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
	protected $_websiteTable;

	protected $_stagingStoreTable;

    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_website', 'staging_website_id');

        $this->_itemTable = $this->getTable('enterprise_staging/staging_item');

        $this->_websiteTable = $this->getTable('core/website');

        $this->_stagingStoreTable = $this->getTable('enterprise_staging/staging_store');
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

        $value = (string) $this->formatDate($object->getApplyDate());
        if ($value) {
            $object->setApplyDate($value);
        } else {
            $object->setApplyDate('0000-00-00 00:00:00');
        }

        $value = (string) $this->formatDate($object->getRollbackDate());
        if ($value) {
            $object->setRollbackDate($value);
        } else {
            $object->setRollbackDate('0000-00-00 00:00:00');
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

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->saveItems($object);

        $this->saveSlaveWebsite($object);

        $this->saveStores($object);

        parent::_afterSave($object);

        return $this;
    }

    public function saveItems($website)
    {
        foreach ($website->getItemsCollection() as $item) {
            $item->save();
        }

        return $this;
    }

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
        $slaveWebsite->save();

        if (!$slaveWebsiteId) {
            $slaveWebsiteId = (int) $slaveWebsite->getId();
            $this->updateAttribute($object, 'slave_website_id', $slaveWebsiteId);
        }

        return $this;
    }

    public function saveStores($website)
    {
        foreach ($website->getStoresCollection() as $store) {
            $store->save();
        }

        return $this;
    }

    public function updateAttribute($website, $name, $value)
    {
        $where = "staging_website_id=".(int)$website->getId();
        $this->_getWriteAdapter()
           ->update($this->getMainTable(), array($name => $value), $where);
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
           ->from($this->_websiteTable, 'website_id')
           ->where('code = ?', $code);

       return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     *
     */
    public function getStoreIds(Enterprise_Staging_Model_Staging_Website $website)
    {
        if (!$website->getId()) {
            return array();
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->_stagingStoreTable, array('staging_store_id'))
            ->where('staging_website_id=?', $website->getId());
        return $this->_getReadAdapter()->fetchCol($select);
    }
}