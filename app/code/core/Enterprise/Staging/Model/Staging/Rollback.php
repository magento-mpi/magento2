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


class Enterprise_Staging_Model_Staging_Rollback extends Mage_Core_Model_Abstract
{
    /**
     * Staging instance
     *
     * @var Enterprise_Staging_Model_Staging
     */
    protected $_staging;

    /**
     * Backup instance
     *
     * @var Enterprise_Staging_Model_Staging
     */
    protected $_backup;

    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_rollback');
    }

    /**
     * Declare staging instance
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_Rollback
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
     * Declare backup instance
     *
     * @param   Enterprise_Staging_Model_Staging_Backup $backup
     * @return  Enterprise_Staging_Model_Staging_Rollback
     */
    public function setBackup(Enterprise_Staging_Model_Staging_Backup $backup)
    {
        $this->_backup = $backup;
        return $this;
    }

    /**
     * Retrieve backup instance
     *
     * @return Enterprise_Staging_Model_Staging_Rollback
     */
    public function getBackup()
    {
        if (!$this->_backup instanceof Enterprise_Staging_Model_Staging_Backup) {
            $this->_backup = Mage::registry('staging_backup');
        }
        return $this->_backup;
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
     * save rollback state in db
     *
     * @param   Enterprise_Staging_Model_Staging_State_Abstract $state
     * @param   Enterprise_Staging_Model_Staging $staging
     * 
     * @return Enterprise_Staging_Model_Staging_Rollback 
     */
    public function saveFromEvent(Enterprise_Staging_Model_Staging_State_Abstract $state, Enterprise_Staging_Model_Staging $staging)
    {
        if ($staging && $staging->getId()) {
            
            $name = Mage::helper('enterprise_staging')->__('Staging rollback: ') . $staging->getName();
            
            $tablePrefix = Enterprise_Staging_Model_Staging_Config::getTablePrefix($staging) 
                           . Enterprise_Staging_Model_Staging_Config::getStagingBackupTablePrefix()
                           . $staging->getEventId() . "_";
            
            $this->setStagingId($staging->getId())
                ->setBackupId($this->getBackup()->getBackupId())
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
            $state->setRollbackId($this->getId());
        }
        return $this;
    }
    
}