<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


class Enterprise_Staging_Model_Staging_Backup extends Mage_Core_Model_Abstract
{
    /**
     * Staging instance
     *
     * @var Enterprise_Staging_Model_Staging
     */
    protected $_staging;

    /**
     * Staging Event instance
     *
     * @var Enterprise_Staging_Model_Staging_Event
     */
    protected $_event;

    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_backup');
    }

    /**
     * Declare staging instance
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_Backup
     */
    public function setStaging(Enterprise_Staging_Model_Staging $staging)
    {
        $this->_staging = $staging;
        return $this;
    }

    /**
     * Retrieve staging instance
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!$this->_staging instanceof Enterprise_Staging_Model_Staging) {
            $this->_staging = Mage::registry('staging');
            if ($this->getId()) {
                $stagingId = $this->getStagingId();
                if ($stagingId) {
                    if (!$this->_staging || ($this->_staging->getId() != $stagingId)) {
                        $this->_staging = Mage::getModel('enterprise_staging/staging')->load($stagingId);
                    }
                }
            }
        }
        return $this->_staging;
    }

    /**
     * Declare staging event instance
     *
     * @param   Enterprise_Staging_Model_Staging_Event $event
     * @return  Enterprise_Staging_Model_Staging_Backup
     */
    public function setEvent(Enterprise_Staging_Model_Staging_Event $event)
    {
        $this->_event = $event;
        return $this;
    }

    /**
     * Retrieve staging event instance
     *
     * @return Enterprise_Staging_Model_Staging_Event
     */
    public function getEvent()
    {
        if (!$this->_event instanceof Enterprise_Staging_Model_Staging_Event) {
            $this->_event = Mage::registry('staging_event');
            if ($this->getId()) {
                $eventId = $this->getEventId();
                if ($eventId) {
                    if (!$this->_event || ($this->_event->getId() != $eventId)) {
                        $this->_event = Mage::getModel('enterprise_staging/staging_event')->load($eventId);
                    }
                }
            }
        }
        return $this->_event;
    }

    /**
     * Retrieve event state label
     *
     * @return string
     */
    public function getStateLabel()
    {
        return Enterprise_Staging_Model_Staging_Config::getStateLabel($this->getState());
    }

    /**
     * Retrieve event status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return Enterprise_Staging_Model_Staging_Config::getStatusLabel($this->getStatus());
    }

    /**
     * Retrieve event label
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        return Enterprise_Staging_Model_Staging_Config::getEventLabel($this->getCode());
    }

    /**
     * Update backup attribute
     *
     * @param string $attribute
     * @param undefined $value
     * @return ResourceModel
     */
    public function updateAttribute($attribute, $value)
    {
        return $this->getResource()->updateAttribute($this, $attribute, $value);
    }

    /**
     * save backup state in db
     *
     * @param   Enterprise_Staging_Model_Staging_State_Abstract $state
     * @param   Enterprise_Staging_Model_Staging $staging
     *
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    public function saveFromState(Enterprise_Staging_Model_Staging_State_Abstract $state, Enterprise_Staging_Model_Staging $staging)
    {
        if ($staging->getId()) {
            $name = Mage::helper('enterprise_staging')->__('Backup: ') . $staging->getName();

            $tablePrefix = Enterprise_Staging_Model_Staging_Config::getTablePrefix($staging)
            . Enterprise_Staging_Model_Staging_Config::getStagingBackupTablePrefix()
            . $state->getEventId() . "_";

            $this->setStagingId($staging->getId())
                ->setEventId($state->getEventId())
                ->setEventCode($state->getEventStateCode())
                ->setName($name)
                ->setState(Enterprise_Staging_Model_Staging_Config::STATE_COMPLETE)
                ->setStatus(Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE)
                ->setCreatedAt(Mage::registry($state->getCode() . "_event_start_time"))
                ->setStagingTablePrefix($tablePrefix)
                ->setMageVersion(Mage::getVersion())
                ->setMageModulesVersion(serialize(Enterprise_Staging_Model_Staging_Config::getCoreResourcesVersion()));
            $this->save();

            $staging->save();

            $state->setBackupId($this->getId());
        }
        return $this;
    }

    /**
     * check rollback condition. If module version changed we can't rollback db
     *
     * @return bool
     */
    public function canRollback()
    {
        if (!$this->getId()) {
            return false;
        }

        if (Mage::helper('enterprise_staging')->getCatalogIndexRunningFlag()) {
            return false;
        }

        if ($this->getStaging() && $this->getStaging()->isStatusProcessing()) {
            return false;
        }

        $itemInfo = $this->getItemVersionCheck();

        if (empty($itemInfo)) {
            return true;
        }

        foreach($itemInfo AS $item) {
            if($item["disabled"]==false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check items modules versions
     * return item version check array
     *
     * @return mixed
     */
    public function getItemVersionCheck()
    {
        if (!$this->getId()) {
            return false;
        }

        // get all current module version
        $currentModuleVersion = Enterprise_Staging_Model_Staging_Config::getCoreResourcesVersion();

        //get backup version list
        $backupModules = unserialize($this->getMageModulesVersion());

        $itemVersionCheck = array();

        $stagingItems = Enterprise_Staging_Model_Staging_Config::getStagingItems();

        foreach ($stagingItems->children() as $stagingItem) {
            if ((int)$stagingItem->is_backend) {
                continue;
            }

            $this->_addStagingItemVersionInfo($itemVersionCheck, $stagingItem, $currentModuleVersion, $backupModules);

            if (!empty($stagingItem->extends) && is_object($stagingItem->extends)) {
                foreach ($stagingItem->extends->children() AS $extendItem) {
                    if (!Enterprise_Staging_Model_Staging_Config::isItemModuleActive($extendItem)) {
                         continue;
                    }
                    $this->_addStagingItemVersionInfo($itemVersionCheck, $extendItem, $currentModuleVersion, $backupModules);
                }
            }

        }

        return $itemVersionCheck;
    }

    protected function _addStagingItemVersionInfo(&$itemVersionCheck, $stagingItem, $currentModuleVersion, $backupModules)
    {
        $itemModel = (string) $stagingItem->model;
        $itemCode  = (string) $stagingItem->code;
        $itemCheckModuleName = $itemModel . "_setup";

        if (isset($backupModules[$itemCheckModuleName])) {
            $itemVersionCheck[$itemCode]["model"] = $itemModel;
            $itemVersionCheck[$itemCode]["backupVersion"] = $backupModules[$itemCheckModuleName];
            $itemVersionCheck[$itemCode]["currentVersion"] =  $currentModuleVersion[$itemCheckModuleName];
            if ($backupModules[$itemCheckModuleName] == $currentModuleVersion[$itemCheckModuleName]) {
                $itemVersionCheck[$itemCode]["disabled"] = false;
                $itemVersionCheck[$itemCode]["note"] = Mage::helper('enterprise_staging')->__('ok');
            } else {
                $itemVersionCheck[$itemCode]["disabled"] = true;
                $itemVersionCheck[$itemCode]["reason"] = Mage::helper('enterprise_staging')->__('version mismatch');
                $itemVersionCheck[$itemCode]["note"] =
                    Mage::helper('enterprise_staging')->__('Backup version: ') . ' ' .
                    $backupModules[$itemCheckModuleName]. " ,".
                    Mage::helper('enterprise_staging')->__('Current: ') .
                    $currentModuleVersion[$itemCheckModuleName];
            }
        } else {
            $itemVersionCheck[$itemCode]["disabled"] = true;
            $itemVersionCheck[$itemCode]["reason"] = Mage::helper('enterprise_staging')->__('unknown item');
            $itemVersionCheck[$itemCode]["note"] =
                Mage::helper('enterprise_staging')->__('Item model "%s" is not under backup', $itemModel);
        }

        return $this;
    }

    public function restoreMap()
    {
        $mergeMap = $this->getMergeMap();
        if (!empty($mergeMap)) {
            $this->getStaging()->getMapperInstance()->unserialize($mergeMap);
        }
        return $this;
    }
}