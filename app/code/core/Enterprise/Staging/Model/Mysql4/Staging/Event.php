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
            $value = Mage::getModel('core/date')->gmtDate();
            $object->setCreatedAt($value);
        } else {
            $value = Mage::getModel('core/date')->gmtDate();
            $object->setUpdatedAt($value);
        }

        $ip = Mage::app()->getRequest()->getServer('REMOTE_ADDR');
        $object->setIp(ip2long($ip));

        $user = Mage::getSingleton('admin/session')->getUser();
        if ($user) {
            $object->setUserId($user->getId());
            $object->setUsername($user->getName());
        }

        parent::_beforeSave($object);

        return $this;
    }
    
    /**
     * Return margeMap for processing websites
     *
     * @return array
     */
    public function getProcessingWebsites()
    {
        $coreResource = Mage::getSingleton('core/resource');
        $connection = $coreResource->getConnection('core_read');
        $select = $connection->select()->from($coreResource->getTableName('enterprise_staging/staging_event'), array('merge_map'))
            ->where("status='processing'");

        $result = $connection->fetchAll($select);

        if (is_array($result) && count($result)>0) {
            return $result;
        } else {
            return array();
        }
    }
    
    /**
     * get bool result if website in processing now 
     *
     * @param int website
     * @return bool
     */
    public function isWebsiteInProcessing($currentWebsiteId)
    {
        if (empty($currentWebsiteId)) {
            return false;
        }
        $eventProcessingSites = self::getProcessingWebsites();
        foreach($eventProcessingSites AS $margeMap){
            $margeMap = unserialize($margeMap['merge_map']);
            if (!empty($margeMap['_slaveWebsitesToMasterWebsites'])){
                foreach ($margeMap['_slaveWebsitesToMasterWebsites'] AS $masterId => $slaveMap){
                    $websiteIds = array_merge(array_keys($slaveMap['master_website']), array_values($slaveMap['master_website']));
                    if (in_array($currentWebsiteId, $websiteIds)) {
                        return true;                                                                
                    }
                }
            }
        }
        return false;
    }
}