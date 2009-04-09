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

/**
 * Enterprise Staging Observer class.
 */
class Enterprise_Staging_Model_Observer
{
    /**
     * Get staging table name for the entities while staging website browse
     *
     * @param $observer Varien_Object
     *
     */
    public function getTableName($observer)
    {
        if (!Mage::app()->isInstalled()) {
            return $this;
        }
        if (Mage::app()->getStore()->isAdmin()) {
            return $this;
        }

        try {

            $resource = $observer->getEvent()->getResource();
            $tableName = $observer->getEvent()->getTableName();
            $modelEntity = $observer->getEvent()->getModelEntity();
            $website = Mage::app()->getWebsite();
            $_tableName = '';
            if ($website->getIsStaging()) {
                $_tableName = Enterprise_Staging_Model_Staging_Config::getStagingTableName($tableName, $modelEntity);
            }

            if ($_tableName) {
                $resource->setMappedTableName($tableName, $_tableName);
            }
        } catch (Enterprise_Staging_Exception $e) {
            throw new Mage_Core_Exception($e);
        }
    }

    /**
     * observer execute before frontend init
     *
     */
    public function beforeFrontendInit()
    {
        $website = Mage::app()->getWebsite();
        if ($website->getIsStaging()) {
            $stagingWebsite = Mage::getModel('enterprise_staging/staging_website');
            $stagingWebsite->loadBySlaveWebsiteId($website->getId());
            if (!$stagingWebsite->getId()) {
                Mage::app()->getResponse()->setRedirect('/')->sendResponse();
                exit();
            }

            $key = 'allow_view_staging_website_'.$website->getCode();
            $coreSession = Mage::getSingleton('core/session');

            switch ($stagingWebsite->getVisibility()) {
                case Enterprise_Staging_Model_Staging_Config::VISIBILITY_NOT_ACCESSIBLE :
                    $coreSession->setData($key, false);
                    Mage::app()->getResponse()->setRedirect('/')->sendResponse();
                    exit();
                    break;
                case Enterprise_Staging_Model_Staging_Config::VISIBILITY_ACCESSIBLE :
                    $coreSession->setData($key, true);
                    break;
                case Enterprise_Staging_Model_Staging_Config::VISIBILITY_REQUIRE_HTTP_AUTH :
                    $this->_checkHttpAuth($key);
                    break;
                case Enterprise_Staging_Model_Staging_Config::VISIBILITY_REQUIRE_ADMIN_SESSION :
                    $this->_checkAdminSession($key);
                    break;
                case Enterprise_Staging_Model_Staging_Config::VISIBILITY_REQUIRE_BOTH :
                    $this->_checkHttpAuth($key);
                    $this->_checkAdminSession($key);
                    break;
            }
        }
    }

    /**
     * check admin session, to apply staging website settings
     *
     * @param dataset $key
     */
    protected function _checkAdminSession($key)
    {
        $coreSession = Mage::getSingleton('core/session');

        if (!Mage::getSingleton('admin/session')->isLoggedIn()) {
            $coreSession->setData($key, false);
            Mage::app()->getResponse()->setRedirect('/')->sendResponse();
            exit();
        } else {
            $coreSession->setData($key, true);
        }
    }

    /**
     * check http auth on staging website loading
     *
     * @param dataset $key
     */
    protected function _checkHttpAuth($key)
    {
        $coreSession = Mage::getSingleton('core/session');

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $login      = $_SERVER['PHP_AUTH_USER'];
            $password   = $_SERVER['PHP_AUTH_PW'];
            $website    = Mage::getModel('enterprise_staging/staging_website');

            try {
                $website->authenticate($login, $password);
                $coreSession->setData($key, true);
            } catch (Exception $e) {
                $coreSession->setData($key, false);
                header('WWW-Authenticate: Basic realm="Staging Site Authentication"');
                header('HTTP/1.0 401 Unauthorized');
                exit();
            }
        }
        if (!$coreSession->getData($key) || !isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Staging Site Authentication"');
            header('HTTP/1.0 401 Unauthorized');
            echo "Please, use right login and password for staging website viewing \n";
            exit();
        }
    }

    /**
     * automate/crontab processing, check and execute all scheduled actions
     *
     */
    public function automates()
    {
        try {
            $currentDate = Mage::getModel('core/date')->gmtDate();

            $collection = Mage::getResourceModel('enterprise_staging/staging_event_collection');

            $collection->addHoldedFilter();

            foreach ($collection as $event) {

                if ($event->getStatus() == Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED) {

                    $applyDate = $event->getMergeScheduleDate();

                    $stagingId = $event->getStagingId();

                    if ($currentDate <= $applyDate) {
                        if ($stagingId){
                            $staging = Mage::getModel('enterprise_staging/staging')->load($stagingId);

                            $staging->setEventId($event->getId());

                            $mapData = $event->getMergeMap();


                            if (!empty($mapData)) {
                                $staging->getMapperInstance()->unserialize($mapData);

                                if ($event->getIsBackuped() == true) {
                                    $staging->backup();
                                }

                                $staging->merge();
                            }
                        }
                    }
                }
            }
        } catch (Enterprise_Staging_Exception $e) {
            throw new Mage_Core_Exception(e);
        }
    }

    /**
     * perform action on master store save
     *
     * @param Enterprise_Staging_Model_Observer $observer
     * @return Enterprise_Staging_Model_Observer
     */
    public function saveStore($observer)
    {
        try {
            $store = $observer->getEvent()->getStore();
            /* @var $store Mage_Core_Model_Store */
            $website = $store->getWebsite();
            /* @var $website Mage_Core_Model_Website */
            if (!$website->getIsStaging()) {
                return $this;
            }

            $stagingStore = Mage::getModel('enterprise_staging/staging_store');
            /* @var $stagingStore Enterprise_Staging_Model_Staging_Store */
            $stagingStore->loadBySlaveStoreId($store->getId());

            $stagingStore->syncWithStore($store);
        } catch (Exception $e) {

        }

        return $this;
    }

    /**
     * perform action on master group save
     *
     * @param Enterprise_Staging_Model_Observer $observer
     * @return Enterprise_Staging_Model_Observer
     */
    public function saveStoreGroup($observer)
    {
        try {
            $group = $observer->getEvent()->getStoreGroup();
            /* @var $group Mage_Core_Model_Store_Group */
            $website = $group->getWebsite();
            /* @var $website Mage_Core_Model_Website */
            if (!$website->getIsStaging()) {
                return $this;
            }

            $stagingGroup = Mage::getModel('enterprise_staging/staging_store_group');
            /* @var $stagingStore Enterprise_Staging_Model_Staging_Store_Group */
            $stagingGroup->loadBySlaveStoreGroupId($group->getId());

            $stagingGroup->syncWithStoreGroup($group);
        } catch (Exception $e) {

        }

        return $this;
    }

    /**
     * perform action on master website save
     *
     * @param Enterprise_Staging_Model_Observer $observer
     * @return Enterprise_Staging_Model_Observer
     */
    public function saveWebsite($observer)
    {
        try {
            $website = $observer->getEvent()->getWebsite();
            /* @var $website Mage_Core_Model_Website */

            $websiteId = $website->getId();

            $_website = Mage::app()->getWebsite($websiteId);
            if (!$_website || !$_website->getIsStaging()) {
                return $this;
            }

            $stagingWebsite = Mage::getModel('enterprise_staging/staging_website');
            /* @var $stagingWebsite Enterprise_Staging_Model_Staging_Website */
            $stagingWebsite->loadBySlaveWebsiteId($websiteId);

            $stagingWebsite->syncWithWebsite($website);
        } catch (Exception $e) {

        }

        return $this;
    }

    /**
     * perform action on master website delete
     *
     * @param Enterprise_Staging_Model_Observer $observer
     * @return Enterprise_Staging_Model_Observer
     */    
    public function deleteWebsite($observer)
    {
        try {
            $website = $observer->getEvent()->getWebsite();
            
            $websiteId = $website->getId();

            $_website = Mage::app()->getWebsite($websiteId);

            if (!$_website || !$_website->getIsStaging()) {
                return $this;
            }

            $stagingWebsite = Mage::getModel('enterprise_staging/staging_website');
            
            /* @var $stagingWebsite Enterprise_Staging_Model_Staging_Website */
            $stagingWebsite->loadBySlaveWebsiteId($websiteId);
            
            
            $stagingId = $stagingWebsite->getStagingId();
            if (!empty($stagingId)) {
                $staging    = Mage::getModel('enterprise_staging/staging');
                $staging->load($stagingId);
            
                $backupCollection = Mage::getResourceModel('enterprise_staging/staging_backup_collection')->setStagingFilter($stagingId);
            
                foreach ($backupCollection as $backup) {
                    if ($backup->getId() > 0) {
                        $backup->setStaging($staging);
                        $backup->setIsDeleteTables(true);
                        $backup->delete();
                    }
                }
    
                Mage::dispatchEvent('enterprise_staging_controller_staging_delete', array('staging'=>$staging));
                $staging->delete();
            }
                
        } catch (Exception $e) {

        }

        return $this;
    }
    
    /**
     * Take down entire frontend if required
     *
     * @param Varien_Event_Observer $observer
     */
    public function takeFrontendDown($observer)
    {
        $result = $observer->getResult();
        if ($result->getShouldProceed() && (bool)Mage::getStoreConfig('general/content_staging/block_frontend')) {
            
            $currentSiteId = Mage::app()->getWebsite()->getId();

            // check whether frontend should be down
            $isNeedToDisable = false;
            
            if ((int)Mage::getStoreConfig('general/content_staging/block_frontend')===1) {
                $eventProcessingSites = Mage::getResourceSingleton('enterprise_staging/staging_event')
                    ->getProcessingWebsites();
                if (count($eventProcessingSites)>0){
                    $isNeedToDisable = true;
                }
            }
            
            if ((int)Mage::getStoreConfig('general/content_staging/block_frontend')===2) {
                 $isNeedToDisable = Mage::getResourceSingleton('enterprise_staging/staging_event')
                    ->isWebsiteInProcessing($currentSiteId);
            }
            
            if ($isNeedToDisable===true) {
                // take the frontend down
                
                $controller = $observer->getController();
                
                if ($controller->getFullActionName() !== 'staging_index_stub') {
                    $controller->getRequest()
                        ->setModuleName('staging')
                        ->setControllerName('index')
                        ->setActionName('stub')
                        ->setDispatched(false);
                    $controller->getResponse()->setHeader('HTTP/1.1','503 Service Unavailable');
                    $result->setShouldProceed(false);
                }
            } 
            return $this;
        }
    }
}