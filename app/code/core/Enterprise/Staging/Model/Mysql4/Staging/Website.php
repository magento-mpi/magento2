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
	protected $_storeTable;

    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_website', 'staging_website_id');

        $this->_websiteTable = $this->getTable('core/website');

        $this->_storeGroupTable = $this->getTable('core/store_group');

        $this->_storeTable = $this->getTable('core/store');

        $this->_stagingStoreTable = $this->getTable('enterprise_staging/staging_store');
    }

    /**
     * Before save processing
     *
     * @param Varien_Object $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
    	$staging = $object->getStaging();
    	if ($staging) {
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
            $value = Mage::getModel('core/date')->gmtDate();
            $object->setCreatedAt($value);
        } else {
            $value = Mage::getModel('core/date')->gmtDate();
            $object->setUpdatedAt($value);
        }

    	parent::_beforeSave($object);

        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
    	$this->saveSlaveWebsite($object);

    	//$this->saveSlaveStoreGroups($object);

        //$this->saveSlaveStores($object);

        parent::_afterSave($object);

        return $this;
    }

    public function saveSlaveWebsite(Mage_Core_Model_Abstract $object)
    {
    	$slaveWebsiteId = (int) $object->getSlaveWebsiteId();
    	$slaveWebsite = Mage::getModel('core/website');

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
            $slaveWebsiteId = $slaveWebsite->getId();
            $this->_updateAttribute($object, 'slave_website_id', $slaveWebsiteId);
        }

        return $this;
    }

//    public function saveSlaveStoreGroups(Mage_Core_Model_Abstract $object)
//    {
//        return $this;
//    }
//
//    public function saveSlaveStores(Mage_Core_Model_Abstract $object)
//    {
//    	return $this;
//    }

    protected function _updateAttribute($website, $name, $slaveWebsiteId)
    {
        $where = "staging_website_id=".$website->getId();
        $this->_getWriteAdapter()
           ->update($this->getMainTable(), array($name => $slaveWebsiteId), $where);
    }

    public function generateWebsiteCode($code)
    {
    	$unusedCode = $this->getUnusedWebsiteCode($code);

    	return  $unusedCode . $this->getWebsiteCodeSuffix();
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

    public function generateStoreCode($code)
    {
        $unusedCode = $this->getUnusedStoreCode($code);

        return  $unusedCode . $this->getStoreCodeSuffix();
    }

    public function getUnusedStoreCode($code)
    {
        if (empty($code)) {
            $code = '-';
        } elseif ($code == $this->getStoreCodeSuffix()) {
            $code = '-' . $this->getStoreCodeSuffix();
        }

        $store = $this->getStoreByCode($code);
        if ($store) {
            // retrieve code suffix for staging stores
            $storeCodeSuffix = $this->getStoreCodeSuffix();

            $match = array();
            if (!preg_match('#^([0-9a-z/-]+?)(-([0-9]+))?('.preg_quote($storeCodeSuffix).')?$#i', $code, $match)) {
                return $this->getUnusedStoreCode('-');
            }
            $code = $match[1].(isset($match[3])?'-'.($match[3]+1):'-1').(isset($match[4])?$match[4]:'');
            return $this->getUnusedStoreCode($code);
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

    /**
     * Retrieve store code sufix
     *
     * @return string
     */
    public function getStoreCodeSuffix()
    {
        return Mage::helper('enterprise_staging/store')->getCodeSuffix();
    }

    public function getWebsiteByCode($code)
    {
        $select = $this->_getReadAdapter()->select()
           ->from($this->_websiteTable, 'website_id')
           ->where('code = ?', $code);

       return $this->_getReadAdapter()->fetchOne($select);
    }

    public function getStoreByCode($code)
    {
        $select = $this->_getReadAdapter()->select()
           ->from($this->_storeTable, 'store_id')
           ->where('code = ?', $code);

       return $this->_getReadAdapter()->fetchOne($select);
    }
}