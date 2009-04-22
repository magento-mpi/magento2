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

    /**
     * Update event attribute
     *
     * @param string $attribute
     * @param any_type $value
     * @return Mage_Core_Model_Abstract
     */
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
     * get Event Comment
     *
     * @param unknown_type $eventLabel
     * @param unknown_type $eventStatus
     * @return string
     */
    public function _getEventComment($eventLabel, $eventStatus, $aditional)
    {
        $confPath = "type/website/scenaries_status/" . $eventStatus;
        $config = Enterprise_Staging_Model_Staging_Config::getConfig($confPath);

        if (!is_object($config)) {
            return "";
        }

        $comment = (string) $config->label;

        $comment = Mage::helper('enterprise_staging')->__('%s '.$comment, $eventLabel);

        if (isset($aditional)) {
            $comment .= ' ' . $aditional;
        }

        return $comment;
    }


    /**
     * save event state in db
     *
     * @param   Enterprise_Staging_Model_Staging_State_Abstract $state
     * @param   Enterprise_Staging_Model_Staging $staging
     *
     * @return Enterprise_Staging_Model_Staging_Event
     */
    public function saveFromState(Enterprise_Staging_Model_Staging_State_Abstract $state, Enterprise_Staging_Model_Staging $staging)
    {
        if ($staging && $staging->getId()) {

            if ($staging->getIsMergeLater()==true) {
                $status = Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED;
            } elseif ($staging->getIsNewStaging()==true) {
                $status = Enterprise_Staging_Model_Staging_Config::STATUS_NEW;
            } else {
                $status = $staging->getStatus();
            }

            $staging->setStatus($status);

            $statusesLabel = $state->getEventStateStatuses();
            $comment = "";
            $scheduleDate = $staging->getMergeSchedulingDate();
            $scheduleOriginDate = $staging->getMergeSchedulingOriginDate();

            if (!empty($statusesLabel)) {
                $comment = $statusesLabel->$status;
            }

            if (!empty($scheduleOriginDate)) {
                $comment .= " " . $scheduleOriginDate;
            }

            $this->setStagingId($staging->getId())
                ->setCode($state->getEventStateCode())
                ->setName($state->getEventStateLabel())
                ->setState(Enterprise_Staging_Model_Staging_Config::STATE_COMPLETE)
                ->setStatus($status)
                ->setIsAdminNotified(false)
                ->setComment($comment)
                ->setMergeMap($staging->getMapperInstance()->serialize())
                ->setMergeScheduleDate($scheduleDate)
                ->setIsBackuped($staging->getIsBackuped())
                ->setStaging($staging);

            $this->save();

            if ($staging->getIsNewStaging()==false) {
                $staging->save();
            }

            $state->setEventId($this->getId());
        }
        return $this;
    }

}