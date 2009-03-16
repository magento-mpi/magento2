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


class Enterprise_Staging_Model_Mysql4_Staging extends Mage_Core_Model_Mysql4_Abstract
{
	protected $_itemTable;

	protected $_websiteTable;

    protected function _construct()
    {
        $this->_init('enterprise_staging/staging', 'staging_id');

        $this->_itemTable = $this->getTable('enterprise_staging/staging_item');

        $this->_websiteTable = $this->getTable('enterprise_staging/staging_website');
    }

    /**
     * Before save processing
     *
     * @param Varien_Object $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
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
        $this->saveItems($object);

        $this->saveWebsites($object);

        $this->saveEvents($object);

        parent::_afterSave($object);

        return $this;
    }

    public function saveItems($staging)
    {
        foreach ($staging->getItemsCollection() as $item) {
            $item->save();
        }

    	return $this;
    }

    public function saveWebsites($staging)
    {
        foreach ($staging->getWebsitesCollection() as $website) {
            $website->save();
        }

        return $this;
    }

    public function saveEvents($staging)
    {
        foreach ($staging->getEventsCollection() as $event) {
            $event->save();
        }

        return $this;
    }

    /**
     * TODO need to remove this method if it no needed more
     */
    public function getItemIds(Enterprise_Staging_Model_Staging $staging)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->_itemTable, array('staging_item_id'))
            ->where('staging_id=?', $staging->getId());
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * TODO need to remove this method if it no needed more
     */
    public function getItemsCollection(Enterprise_Staging_Model_Staging $staging)
    {
    	return Mage::getResourceModel('enterprise_staging/staging_item_collection')
            ->addStagingFilter($staging);
    }

    /**
     *
     */
    public function getWebsiteIds(Enterprise_Staging_Model_Staging $staging)
    {
    	if (!$staging->getId()) {
    		return array();
    	}

        $select = $this->_getReadAdapter()->select()
            ->from($this->_websiteTable, array('staging_website_id'))
            ->where('staging_id=?', $staging->getId());
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Validate all object's attributes against configuration
     *
     * @param Varien_Object $object
     * @return Varien_Object
     */
    public function validate($object)
    {
        return $this;
    }
}