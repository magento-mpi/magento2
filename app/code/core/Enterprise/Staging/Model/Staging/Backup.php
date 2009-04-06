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


class Enterprise_Staging_Model_Staging_Backup extends Mage_Core_Model_Abstract
{
    /**
     * Staging instance
     *
     * @var Enterprise_Staging_Model_Staging
     */
    protected $_staging;

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
        }
        return $this->_staging;
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
    public function saveFromEvent(Enterprise_Staging_Model_Staging_State_Abstract $state, Enterprise_Staging_Model_Staging $staging)
    {
        if ($staging && $staging->getId()) {
            
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
    
    public function getItemVersionCheck()
    {
        if (!$this->getId()) {
            return false;
        }
        
        // get all current module version
        $currentModuleVersion = Enterprise_Staging_Model_Staging_Config::getCoreResourcesVersion();

        //get backup version list
        $backupModules = unserialize($this->getMageModulesVersion());
        
        //Item List
        $getCurrentItems = Enterprise_Staging_Model_Staging_Config::getConfig("staging_items");
        
        $itemVersionCheck = array();
        
        if (!empty($getCurrentItems) && count($getCurrentItems)>0) {
            foreach($getCurrentItems->children() AS $item) {
                $itemModel = (string) $item->model;
                $itemCode = (string) $item->code;
                $itemCheckModuleName = $itemModel . "_setup";                
                
                $itemVersionCheck[$itemCode]["model"] = $itemModel;
                $itemVersionCheck[$itemCode]["backupVersion"] = $backupModules[$itemCheckModuleName];
                $itemVersionCheck[$itemCode]["currentVersion"] =  $currentModuleVersion[$itemCheckModuleName];
                if ($backupModules[$itemCheckModuleName] == $currentModuleVersion[$itemCheckModuleName]) {
                    $itemVersionCheck[$itemCode]["disabled"] = false;                    
                    $itemVersionCheck[$itemCode]["note"] = Mage::helper('enterprise_staging')->__('ok');    
                } else {
                    $itemVersionCheck[$itemCode]["disabled"] = true;                    
                    $itemVersionCheck[$itemCode]["note"] = 
                        Mage::helper('enterprise_staging')->__('Backup version: ') . ' ' .  
                        $backupModules[$itemCheckModuleName]. " ,". 
                        Mage::helper('enterprise_staging')->__('Current: ') .
                        $currentModuleVersion[$itemCheckModuleName];    
                }
            }
        } 
        return $itemVersionCheck;
    }
}