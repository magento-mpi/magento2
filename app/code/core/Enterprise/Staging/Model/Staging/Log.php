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


class Enterprise_Staging_Model_Staging_Log extends Mage_Core_Model_Abstract
{
    /**
     * Staging instance
     *
     * @var Enterprise_Staging_Model_Staging
     */
    protected $_staging;

    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_log');
    }

    /**
     * Declare staging instance
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_Log
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

    public function restoreMap()
    {
        $map = $this->getMap();
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
     * @return Enterprise_Staging_Model_Staging_Log
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
                $this->setStagingWebsiteId($staging->getStagingWebsiteId());
                $this->setMasterWebsiteId($staging->getMasterWebsiteId());
                break;
            case 'update':
                if ($onState == 'after') {
                    $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_UPDATED;
                }
                $this->setStagingWebsiteId($staging->getStagingWebsiteId());
                $this->setMasterWebsiteId($staging->getMasterWebsiteId());
                break;
            case 'backup':
                $this->setIsBackuped($staging->getIsBackuped());
                if ($onState == 'after') {
                    $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_BACKUP_CREATED;
                }
                $this->setStagingWebsiteId(0);
                $this->setMasterWebsiteId($staging->getMasterWebsiteId());
                break;
            case 'merge':
                if ($staging->getIsMergeLater() == true) {
                    $status = Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED;

                    $scheduleDate       = $staging->getMergeSchedulingDate();
                    $scheduleOriginDate = $staging->getMergeSchedulingOriginDate();

                }
                $this->setMergeMap($staging->getMapperInstance()->serialize());
                if ($onState == 'after') {
                    if ($staging->getIsMergeLater() == true) {
                        $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED;
                    } else {
                        $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_MERGED;
                    }
                }
                $this->setStagingWebsiteId($staging->getMasterWebsiteId());
                $this->setMasterWebsiteId($staging->getStagingWebsiteId());
                break;
            case 'rollback':
                $this->setMergeMap($staging->getMapperInstance()->serialize());
                if ($onState == 'after') {
                    $eventStatus = Enterprise_Staging_Model_Staging_Config::STATUS_RESTORED;
                }
                $this->setStagingWebsiteId($staging->getMasterWebsiteId());
                $this->setMasterWebsiteId(0);
                break;
        }

        if ($currentStatus != Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED) {
            $staging->updateAttribute('status', $status);
        }

        $this->setSaveThrowException($exception);

        $eventStatusLabel = $config->getStatusLabel($eventStatus);

        $comment = $eventStatusLabel;
        if (!empty($scheduleOriginDate)) {
            $comment .= " (scheduled to: " . $scheduleOriginDate . ")";
        }

        $exceptionMessage = '';

        if (!is_null($exception)) {
            $exceptionMessage = $exception->getMessage();
        }

        $this->setStagingId($staging->getId())
            ->setCode($process)
            ->setName($eventStatusLabel)
            ->setStatus($eventStatus)
            ->setIsAdminNotified(false)
            ->setMap($staging->getMapperInstance()->serialize())
            ->setComment($comment)
            ->setLog($exceptionMessage)
            ->save();

        return $this;
    }

    /**
     * Retrieve id of last schedule merge for current staging
     *
     * @return int
     */
    public function getLastScheduleMergeLogId()
    {
        return $this->_getResource()->getLastScheduleMergeLogId($this->getStagingId());
    }
}
