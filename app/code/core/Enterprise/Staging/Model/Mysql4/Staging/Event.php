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

class Enterprise_Staging_Model_Mysql4_Staging_Event extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_event', 'event_id');
    }

    /**
     * Before save processing
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  Enterprise_Staging_Model_Mysql4_Staging_Event
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $staging = $object->getStaging();
        if ($staging instanceof Enterprise_Staging_Model_Staging) {
            if ($staging->getId()) {
                $object->setStagingId($staging->getId());
            }
        }

        if (!$object->getId()) {
            $object->setIsNew(true);
            $value = Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss");
            $object->setCreatedAt($value);
        } else {
            $value = Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss");
            $object->setUpdatedAt($value);
        }

        $ip = Mage::app()->getRequest()->getServer('REMOTE_ADDR');
        $object->setIp($ip);

        $user = Mage::getSingleton('admin/session')->getUser();
        if ($user) {
            $object->setUserId($user->getId());
            $object->setUsername($user->getName());
        }

        parent::_beforeSave($object);

        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getIsNew() && $object->getCode() == Enterprise_Staging_Model_Staging_Config::EVENT_BACKUP) {
            $staging = $object->getStaging();
            if ($staging && $staging->getId()) {
                $name = Mage::helper('enterprise_staging')->__('Staging backup: ') . $staging->getName();
                $backup = Mage::getModel("enterprise_staging/staging_backup")
                    ->setStagingId($staging->getId())
                    ->setEventId($object->getId())
                    ->setEventCode($object->getCode())
                    ->setName($name)
                    ->setState(Enterprise_Staging_Model_Staging_Config::STATE_COMPLETE)
                    ->setStatus(Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE)
                    ->setCreatedAt(Mage::registry($object->getCode() . "_event_start_time"))
                    ->setStagingTablePrefix(Enterprise_Staging_Model_Staging_Config::getTablePrefix($staging))
                    ->setMageVersion(Mage::getVersion())
                    ->setMageModulesVersion(serialize(Enterprise_Staging_Model_Staging_Config::getCoreResourcesVersion()));
                $backup->save();
            }
        }

        $object->setIsNew(false);

        parent::_afterSave($object);
    }
}