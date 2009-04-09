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

class Enterprise_Staging_Model_Mysql4_Staging_Store_Group extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_store_group', 'staging_group_id');
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
            if ($stagingWebsite->getId()) {
                $object->setStagingWebsiteId($stagingWebsite->getId());
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
            //$this->saveSlaveStoreGroup($object);
        }
        parent::_afterSave($object);

        return $this;
    }

    public function saveSlaveStoreGroup(Mage_Core_Model_Abstract $object)
    {
        $slaveStoreGroup   = Mage::getModel('core/store_group');
        $slaveStoreGroupId = (int) $object->getSlaveStoreGroupId();
        if ($slaveStoreGroupId) {
            $slaveStoreGroup->load($slaveStoreGroupId);
        }
        $slaveStoreGroup->setData('is_staging', 1);
        $slaveStoreGroup->setData('website_id', $object->getSlaveWebsiteId());
        $slaveStoreGroup->setData('root_category_id', 2); // TODO quick FIXME quick
        $slaveStoreGroup->setData('name', 'Staging Store Group (autocreated)');

        $slaveStoreGroup->save();

        if (!$slaveStoreGroupId) {
            $slaveStoreGroupId = (int) $slaveStoreGroup->getId();
            $this->updateAttribute($object, 'slave_group_id', $slaveStoreGroupId);
            $object->setSlaveGroupId($slaveStoreGroupId);
        }

        return $this;
    }

    /**
     * Update specific attribute value
     *
     * @param   Enterprise_Staging_Model_Staging_Store_Group $group
     * @param   string    $field
     * @param   mixed     $value
     * @return  Enterprise_Staging_Model_Mysql4_Staging_Store_Group
     */
    public function updateAttribute($group, $field, $value)
    {
        $where = "staging_group_id=".(int)$group->getId();
        $this->_getWriteAdapter()
           ->update($this->getMainTable(), array($field => $value), $where);

       return $this;
    }

    public function loadBySlaveStoreGroupId($group, $id)
    {
        return parent::load($group, $id, 'slave_group_id');
    }

    public function syncWithStoreGroup($object, $group)
    {
        if ($group->getIgnoreSyncStagingGroup()) {
            return $this;
        }
        $now = Mage::getModel('core/date')->gmtDate();

        $object->setData('slave_group_id',      $group->getId());

        $stagingWebsite = Mage::getModel('enterprise_staging/staging_website');
        /* @var $stagingWebsite Enterprise_Staging_Model_Staging_Website */
        $stagingWebsite->loadBySlaveWebsiteId($group->getWebsiteId());
        $object->setData('staging_website_id',  $stagingWebsite->getId());
        $object->setData('staging_id',          $stagingWebsite->getStagingId());
        $object->setData('master_website_id',   $stagingWebsite->getMasterWebsiteId());

        $object->setData('name',                $group->getName());
        $object->setData('root_category_id',    $group->getRootCategoryId());
        $object->setData('default_store_id',    $group->getDefaultStoreId());

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