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

/**
 * Staging model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'enterprise_staging';
    protected $_eventObject = 'staging';
    protected $_tablePrefix = 'staging';

    /**
     * Current staging process name
     *
     * @var string
     */
    protected $_processName;

    /**
     * Staging mapper instance
     *
     * @var mixed Enterprise_Staging_Model_Staging_Mapper_Abstract
     */
    protected $_mapperInstance = null;

    /**
     * Keeps copied staging items collection
     *
     * @var object Varien_Data_Collection
     */
    protected $_items;

    /**
     * Initialize resources
     */
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging');
    }

    public function getTablePrefix()
    {
        $prefix = Enterprise_Staging_Model_Staging_Config::getTablePrefix();
        if ($this->getId()) {
            $prefix .= $this->getId();
        }
        return $prefix;
    }

    /**
     * Validate staging data
     *
     * @return boolean
     */
    public function validate()
    {
        $errors = array();

        $result = $this->_getResource()->validate($this);
        if (!empty($result)) {
        	$errors[] = $result;
        }

        $password = $this->getMasterPassword();
        if ($password && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = Mage::helper('customer')->__('Password minimal length must be more %s', 6);
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    public function getMasterWebsite()
    {
        if ($this->hasData('master_website_id')) {
            return Mage::app()->getWebsite($this->getData('master_website_id'));
        } else {
            return false;
        }
    }

    public function getStagingWebsite()
    {
        if ($this->hasData('staging_website_id')) {
            return Mage::app()->getWebsite($this->getData('staging_website_id'));
        } else {
            return false;
        }
    }

    /**
     * Get staging item codes
     *
     * @return array
     */
    public function getStagingItemCodes()
    {
        if ($this->hasData('staging_item_codes')) {
            $codes = $this->getData('staging_item_codes');
            if (!is_array($codes)) {
                $codes = !empty($codes) ? explode(',', $codes) : array();
                $this->setData('staging_item_codes', $codes);
            }
        } else {
            $codes = array();
            foreach ($this->getItemsCollection() as $item) {
                $codes[] = $item->getCode();
            }
            $this->setData('item_codes', $codes);
        }
        return $this->getData('item_codes');
    }

    /**
     * add item in item collection
     *
     * @param Enterprise_Staging_Model_Staging_Item $item
     * @return Enterprise_Staging_Model_Staging
     */
    public function addItem(Enterprise_Staging_Model_Staging_Item $item)
    {
        $item->setStaging($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Retrieve staging items
     *
     * @return Varien_Data_Collection
     */
    public function getItemsCollection()
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('enterprise_staging/staging_item_collection')
                ->setStagingFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setStaging($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * Check posibility to run some staging action (create, merge etc)
     *
     * @return  boolean
     */
    public function checkCoreFlag()
    {
        $catalogIndexFlag = Mage::getModel('catalogindex/catalog_index_flag')->loadSelf();
        if ($catalogIndexFlag->getState() == Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_RUNNING) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Set core flag for reserve process
     *
     * @return  Enterprise_Staging_Model_Staging
     */
    public function setCoreFlag()
    {
        $catalogIndexFlag = Mage::getModel('catalogindex/catalog_index_flag')
            ->loadSelf()
            ->setState(Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_RUNNING)
            ->save();
        return $this;
    }

    /**
     * Release core flag after process
     *
     * @return  Enterprise_Staging_Model_Staging
     */
    public function releaseCoreFlag()
    {
        $catalogIndexFlag = Mage::getModel('catalogindex/catalog_index_flag')->loadSelf();
        if ($catalogIndexFlag->getState() == Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_RUNNING) {
            $catalogIndexFlag->delete();
        }
        return $this;
    }

    /**
     * Processing object process run
     *
     * @param  string $process
     * @return Enterprise_Staging_Model_Staging
     */
    public function stagingProcessRun($process)
    {
        $this->_processName = $process;

        $event = Mage::getModel('enterprise_staging/staging_event')->saveOnProcessRun($this, $process, 'before');

        $method = $this->_processName.'Run';

        $this->_getResource()->beginTransaction();
        try {
            $this->_beforeStagingProcessRun();

            $this->_getResource()->{$method}($this, $event);

            $this->_afterStagingProcessRun();

            $this->_getResource()->commit();

            $event->saveOnProcessRun($this, $process, 'after');
        }
        catch (Exception $e){
            $this->_getResource()->rollBack();
            $event->saveOnProcessRun($this, $process, 'after', $e);
            throw $e;
        }
        return $this;
    }

    /**
     * Processing object before process run data
     *
     * @return Enterprise_Staging_Model_Staging
     */
    protected function _beforeStagingProcessRun()
    {
        $this->setCoreFlag();
        Mage::dispatchEvent($this->_eventPrefix.'_process_run_before', array($this->_eventObject=>$this));
        Mage::dispatchEvent($this->_eventPrefix.'_'.$this->_processName.'_process_run_before', array($this->_eventObject=>$this));
        return $this;
    }

    /**
     * Perform object after process run data
     *
     * @return Enterprise_Staging_Model_Staging
     */
    protected function _afterStagingProcessRun()
    {
        Mage::dispatchEvent($this->_eventPrefix.'_action_run_after', array($this->_eventObject=>$this));
        Mage::dispatchEvent($this->_eventPrefix.'_'.$this->_processName.'_process_run_after', array($this->_eventObject=>$this));

        $this->releaseCoreFlag();
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
        return $this;
    }

    /**
     * Create staging
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function create()
    {
        if ($this->checkCoreFlag()) {
            $this->stagingProcessRun('create');
        }
        return $this;
    }

    /**
     * Update staging
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function update()
    {
        if ($this->checkCoreFlag()) {
            $this->stagingProcessRun('update');
        }
        return $this;
    }

    /**
     * Merge Staging
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function merge()
    {
        if ($this->checkCoreFlag()) {
            $this->stagingProcessRun('merge');
        }
        return $this;
    }

    /**
     * Backup Staging
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function backup()
    {
        if ($this->checkCoreFlag()) {
            $this->stagingProcessRun('backup');
        }
        return $this;
    }

    /**
     * Rollback Staging
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function rollback()
    {
        if ($this->checkCoreFlag()) {
            $this->stagingProcessRun('rollback');
        }
        return $this;
    }

    /**
     * Check Frontend Website Staging
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function checkFrontend()
    {
        $this->_getResource()->checkfrontendRun($this);

        return $this;
    }

    /**
     * Processing staging after delete
     *
     * @return Enterprise_Staging_Model_Staging
     */
    protected function _afterDelete()
    {
        if ($this->getStagingWebsite()) {
            $this->getStagingWebsite()->delete();
        }
        foreach ($this->getEventsCollection() as $event) {
            $event->delete();
        }
        parent::_afterDelete();
        return $this;
    }

    /**
     * Add event
     *
     * @param   string  $code
     * @param   string  $state
     * @param   string  $status
     * @param   string  $comments
     * @param   boolean $isAdminNotified
     * @return  object  Enterprise_Staging_Model_Staging
     */
    public function addEvent($code, $name, $state, $status, $comment='', $log='', $isAdminNotified = false)
    {
        $event = Mage::getModel('enterprise_staging/staging_event')
            ->setStagingId($this->getId())
            ->setCode($code)
            ->setName($name)
            ->setState($state)
            ->setStatus($status)
            ->setDate(Mage::getModel('core/date')->gmtDate())
            ->setIsAdminNotified($isAdminNotified)
            ->setComment($comment)
            ->setLog($log)
            ->setMergeMap($this->getMapperInstance()->serialize())
            ->setIsBackuped($this->getMapperInstance()->getIsBackupped())
            ->setStaging($this);
        $this->addEventToHistory($event);
        return $this;
    }

    /**
     * Add event to collection
     *
     * @param   string  $event
     * @return  object  Enterprise_Staging_Model_Staging
     */
    public function addEventToHistory(Enterprise_Staging_Model_Staging_Event $event)
    {
        if (!$event->getId()) {
            $this->getEventsCollection()->addItem($event);
        }
        return $this;
    }

    /**
     * Retrieve event collection
     *
     * @param boolean $reload
     * @return Enterprise_Staging_Model_Mysql4_Staging_Event_Collection
     */
    public function getEventsCollection($reload=false)
    {
        if (is_null($this->_eventsCollection) || $reload) {
            $this->_eventsCollection = Mage::getResourceModel('enterprise_staging/staging_event_collection')
                ->setStagingFilter($this->getId())
                ->setOrder('created_at', 'desc')
                ->setOrder('event_id', 'desc');

            if ($this->getId()) {
                foreach ($this->_eventsCollection as $event) {
                    $event->setStaging($this);
                }
            }
        }
        return $this->_eventsCollection;
    }

    /**
     * Retrieve Mapper instance
     *
     * @return Enterprise_Staging_Model_Staging_Mapper_Abstract
     */
    public function getMapperInstance()
    {
        if ($this->_mapperInstance === null) {
            $this->_mapperInstance = Enterprise_Staging_Model_Staging_Config::mapperFactory($this, true);
        }
        return $this->_mapperInstance;
    }

    /**
     * Check if we can save
     *
     * @return boolean
     */
    public function canSave()
    {
        if (!$this->getId()) {
            return false;
        }
        return true;
    }

    /**
     *  Check current status for staging
     *  @return boolean
     */
    public function isStatusProcessing()
    {
         if ($this->getStatus() == Enterprise_Staging_Model_Staging_Config::STATUS_PROCESSING) {
             return true;
         } else {
            return false;
         }
    }

    /**
     * check if we can delete
     *
     * @return boolean
     */
    public function canDelete()
    {
        if (!$this->getId()) {
            return false;
        }
        if (($this->getStatus() == Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED)
            || ($this->getStatus() == Enterprise_Staging_Model_Staging_Config::STATUS_PROCESSING)) {
            return false;
        }
        return true;
    }

    /**
     * chack if reset status button need
     * @return bool
     */
    public function canResetStatus()
    {
        if ($this->getStatus() == Enterprise_Staging_Model_Staging_Config::STATUS_PROCESSING) {
            return true;
        }
        return false;
    }

    /**
     * check if we can merge
     *
     * @return boolean
     */
    public function canMerge()
    {
        if (!$this->getId()) {
            return false;
        }
        if (!$this->checkCoreFlag()) {
            return false;
        }
        if (($this->getStatus() == Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED)) {
            return false;
        }
        return true;
    }

    /**
     * Update staging attribute
     *
     * @param   string  $attribute
     * @param   mixed   $value
     * @return  Enterprice_Staging_Model_Staging
     */
    public function updateAttribute($attribute, $value)
    {
        return $this->getResource()->updateAttribute($this, $attribute, $value);
    }

    /**
     * Save event in event history list
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function saveEventHistory()
    {
        $this->getResource()->saveEvents($this);

        return $this;
    }

    /**
     * Save staging items
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function saveItems()
    {
        $this->getResource()->saveItems($this);

        return $this;
    }

    /**
     * Load staging model by given staging website id
     *
     * @param int $stagingWebsiteId
     * @return Enterprise_Staging_Model_Staging
     */
    public function loadByStagingWebsiteId($stagingWebsiteId)
    {
        $this->load($stagingWebsiteId, 'staging_website_id');

        return $this;
    }
}
