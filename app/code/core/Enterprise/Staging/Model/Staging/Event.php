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
     * @return  Enterprise_Staging_Model_Staging_Event
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
            $this->_staging = Mage::getModel('enterprise_staging/staging')->load($this->getStagingId());
        }
        return $this->_staging;
    }

    /**
     * Retrieve event status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return Mage::getSingleton('enterprise_staging/staging_config')->getStatusLabel($this->getStatus());
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

            foreach ($collection as $backup) {
                if ($backup->getId()) {
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
     * save event in db
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @param   string $process
     * @param   string $onState
     * @param   Exception $exception
     *
     * @return Enterprise_Staging_Model_Staging_Event
     */
    public function saveOnProcessRun(Enterprise_Staging_Model_Staging $staging, $process, $onState, $exception = null)
    {
        $this->setStaging($staging);

        $currentStatus = $staging->getStatus();

        $config = Mage::getSingleton('enterprise_staging/staging_config');

        if ($onState == 'before') {
            $status = $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_PROCESSING;
        } else {
            $status = $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE;
        }

        switch ($process) {
            case 'create':
                if ($onState == 'after') {
                    $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_CREATED;
                }
                break;
            case 'update':
                if ($onState == 'after') {
                    $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_UPDATED;
                }
                break;
            case 'backup':
                $this->setIsBackuped($staging->getIsBackuped());
                if ($onState == 'after') {
                    $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_BACKUP_CREATED;
                }
                break;
            case 'merge':
                if ($staging->getIsMergeLater() == true) {
                    $status = Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED;

                    $scheduleDate       = $staging->getMergeSchedulingDate();
                    $scheduleOriginDate = $staging->getMergeSchedulingOriginDate();
                    $this->setMergeScheduleDate($scheduleDate);
                }
                $this->setMergeMap($staging->getMapperInstance()->serialize());
                if ($onState == 'after') {
                    $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_MERGED;
                }
                break;
            case 'rollback':
                $this->setMergeMap($staging->getMapperInstance()->serialize());
                if ($onState == 'after') {
                    $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_RESTORED;
                }
                break;
        }

        if ($currentStatus != Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED) {
            $staging->updateAttribute('status', $status);
        }

        $this->setSaveThrowException($exception);

        $eventStatusLabel = $config->getStatusLabel($eventStatus);

        $comment = $eventStatusLabel;
        if (!empty($scheduleOriginDate)) {
            $comment .= " scheduled to: " . $scheduleOriginDate;
        }

        $exceptionMessage = '';
        if (!is_null($exception)) {
            $exceptionMessage = $exception->getMessage();
        }
        $this->setCode($process)
            ->setName($eventStatusLabel)
            ->setStatus($eventStatus)
            ->setIsAdminNotified(false)
            ->setComment($comment)
            ->setLog($exceptionMessage)
            ->save();

        if ($staging->getIsMergeLater() == true) {
            $staging->updateAttribute('schedule_merge_event_id',$this->getId());
        }

        return $this;
    }
}
