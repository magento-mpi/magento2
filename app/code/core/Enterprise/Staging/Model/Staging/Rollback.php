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


class Enterprise_Staging_Model_Staging_Rollback extends Mage_Core_Model_Abstract
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

    /**
     * Staging Backup instance
     *
     * @var Enterprise_Staging_Model_Staging_Backup
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
            $this->_staging = Mage::getModel('enterprise_staging/staging')->load($this->getStagingId());
        }
        return $this->_staging;
    }

    /**
     * Declare staging event instance
     *
     * @param   Enterprise_Staging_Model_Staging_Event $event
     * @return  Enterprise_Staging_Model_Staging_Rollback
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
            $this->_event = Mage::getModel('enterprise_staging/staging_event')->load($this->getEventId());
        }
        return $this->_event;
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
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    public function getBackup()
    {
        if (!$this->_backup instanceof Enterprise_Staging_Model_Staging_Backup) {
            $this->_backup = Mage::getModel('enterprise_staging/staging_backup')->load($this->getBackupId());
        }
        return $this->_backup;
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

    public function updateAttribute($attribute, $value)
    {
        return $this->getResource()->updateAttribute($this, $attribute, $value);
    }

    /**
     * save rollback state in db
     *
     * @param  object Enterprise_Staging_Model_Staging $staging
     * @param  object Enterprise_Staging_Model_Staging_Event $event
     *
     * @return Enterprise_Staging_Model_Staging_Rollback
     */
    public function saveOnRollbackRun(Enterprise_Staging_Model_Staging $staging, Enterprise_Staging_Model_Staging_Event $event)
    {
        $backup  = Mage::registry('staging_backup');

        if ($staging->getId()) {
            $name = Mage::helper('enterprise_staging')->__('Staging rollback: %s', $staging->getName());
            $this->setStagingId($staging->getId());
        } else {
            $name = Mage::helper('enterprise_staging')->__('Staging rollback');
        }

        $this->setBackupId($backup->getId())
            ->setEventId($event->getId())
            ->setName($name)
            ->setStatus(Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE)
            ->setCreatedAt(Mage::registry($event->getCode() . "_event_start_time"))
            ->setStagingTablePrefix($this->getBackup()->getStagingTablePrefix())
            ->setMageVersion(Mage::getVersion())
            ->setMageModulesVersion(serialize(Mage::getSingleton('enterprise_staging/staging_config')->getCoreResourcesVersion()))
            ->save();

        return $this;
    }
}
