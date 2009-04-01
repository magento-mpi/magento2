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


class Enterprise_Staging_Model_Staging_Event extends Mage_Core_Model_Abstract
{
    /**
     * Staging instance
     *
     * @var Enterprise_Staging_Model_Staging
     */
    protected $_staging;

    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_event');
    }

    /**
     * Declare staging instance
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_Event_History
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
    
    /**
     * Get backup Id by EventId
     *
     * @return int
     */
    public function getBackupId()
    {
        $eventId = $this->getId();
        if (!empty($eventId)) {
            $collection = Mage::getResourceModel('enterprise_staging/staging_backup_collection');
            
            $collection->setEventFilter($eventId);
                    
            foreach($collection AS $backup) {
                if ($backup->getId()){
                    return $backup->getId(); 
                }
            }
        }
        return 0;
    }

    public function updateAttribute($attribute, $value)
    {
        return $this->getResource()->updateAttribute($this, $attribute, $value);
    }
    
    public function restoreMap()
    {
        $map = $this->getMergeMap();
        if (!empty($map)) {
            $this->getStaging()->getMapperInstance()->unserialize($map);
        }
        return $this;
    }
    
    /**
     * save event state in db
     *
     * @param   Enterprise_Staging_Model_Staging_State_Abstract $state
     * @param   Enterprise_Staging_Model_Staging $staging
     * 
     * @return Enterprise_Staging_Model_Staging_Event 
     */    
    public function saveFromEvent(Enterprise_Staging_Model_Staging_State_Abstract $state, Enterprise_Staging_Model_Staging $staging)
    {
        if ($staging && $staging->getId()) {
            
            if ($staging->getIsMergeLater()==true) {
                $comment = Mage::helper('enterprise_staging')->__('%s was successfuly scheduled.', $state->getEventStateLabel());                
                $status = Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED;
            } else {
                $comment = Mage::helper('enterprise_staging')->__('%s was successfuly finished.', $state->getEventStateLabel());                
                $status = $staging->getStatus();
            }
           
            $this->setStagingId($staging->getId())
                ->setCode($state->getEventStateCode())
                ->setName($state->getEventStateLabel())
                ->setState(Enterprise_Staging_Model_Staging_Config::STATE_COMPLETE)
                ->setStatus($status)
                ->setIsAdminNotified(false)
                ->setComment($comment)
                ->setLog(Enterprise_Staging_Model_Log::buildLogReport(""))
                ->setMergeMap($staging->getMapperInstance()->serialize())
                ->setMergeScheduleDate($staging->getMergeSchedulingDate())
                ->setIsBackuped($staging->getIsBackuped())
                ->setStaging($staging);

            $this->save();
            $state->setEventId($this->getId());
        }        
        return $this;
    } 
}