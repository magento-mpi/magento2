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
 * Staging model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging extends Mage_Core_Model_Abstract
{
    const CACHE_TAG             = 'enterprise_staging';
    protected $_cacheTag        = 'enterprise_staging';
    protected $_eventPrefix     = 'enterprise_staging';
    protected $_eventObject     = 'enterprise_staging';
    protected $_tablePrefix     = 'staging';

    const EXCEPTION_LOGIN_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_LOGIN_OR_PASSWORD = 2;

    /**
     * Staging type instance
     *
     * @var mixed Enterprise_Staging_Model_Staging_Type_Abstract
     */
    protected $_typeInstance                = null;

    /**
     * Staging type instance as singleton
     */
    protected $_typeInstanceSingleton       = null;

    /**
     * Staging mapper instance
     *
     * @var mixed Enterprise_Staging_Model_Staging_Mapper_Abstract
     */
    protected $_mapperInstance              = null;

    /**
     * Staging mapper instance as singleton
     */
    protected $_mapperInstanceSingleton     = null;

    /**
     * Staging resource adapter instance
     *
     * @var mixed Enterprise_Staging_Model_Staging_Mapper_Abstract
     */
    protected $_adapterInstance             = null;

    /**
     * Staging resource adapter instance as singleton
     */
    protected $_adapterInstanceSingleton    = null;

    protected $_datasetItems                = null;

    protected $_items;

    protected $_websites;

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
        $prefix .='_';
        return $prefix;
    }

    /**
     * Authenticate user for frontend view
     *
     * @param  string $login
     * @param  string $password
     * @return true
     * @throws Exception
     */
    public function authenticate($login, $password)
    {
        $this->load($login, 'master_login');
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw new Exception(Mage::helper('enterprise_staging')->__('This account is not confirmed.'), self::EXCEPTION_LOGIN_NOT_CONFIRMED);
        }
        if (!$this->validatePassword($password)) {
            throw new Exception(Mage::helper('enterprise_staging')->__('Invalid login or password.'), self::EXCEPTION_INVALID_LOGIN_OR_PASSWORD);
        }
        return true;
    }

    /**
     * Set plain and hashed password
     *
     * @param string $password
     * @return Enterprise_Staging_Model_Staging
     */
    public function setMasterPassword($password)
    {
        $this->setData('master_password', $password);
        $this->setMasterPasswordHash($this->hashPassword($password));
        return $this;
    }

    /**
     * Hach customer password
     *
     * @param   string $password
     * @return  string
     */
    public function hashPassword($password, $salt=null)
    {
        return Mage::helper('core')->getHash($password, !is_null($salt) ? $salt : 2);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length=6)
    {
        return substr(md5(uniqid(rand(), true)), 0, $length);
    }

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        if (!($hash = $this->getMasterPasswordHash())) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }

    /**
     * Encrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function encryptPassword($password)
    {
        return Mage::helper('core')->encrypt($password);
    }

    /**
     * Decrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function decryptPassword($password)
    {
        return Mage::helper('core')->decrypt($password);
    }


    /**
     * Validate staging data
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();

        $result = $this->_getResource()->validate($this);
        if (!empty($result)) {
        	$errors[] = $result;
        }

        if (!Zend_Validate::is(trim($this->getLogin()) , 'NotEmpty')) {
            $errors[] = Mage::helper('enterprise_staging')->__('Login can\'t be empty');
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
            $errors[] = Mage::helper('enterprise_staging')->__('Password can\'t be empty');
        }
        if ($password && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = Mage::helper('enterprise_staging')->__('Password minimal length must be more %s', 6);
        }
        $confirmation = $this->getConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('enterprise_staging')->__('Please make sure your passwords match.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Get staging name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getData('name');
    }

    /**
     * Get staging type identifier
     *
     * @return int
     */
    public function getType()
    {
        if (!$this->hasData('type')) {
            $this->setData('type', 'website');
        }
        return $this->getData('type');
    }

    /**
     * Get staging status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->_getData('status');
    }

    /**
     * Retrive staging id by code
     *
     * @param   string $code
     * @return  integer
     */
    public function getIdByCode($code)
    {
        return $this->_getResource()->getIdByCode($code);
    }

    public function getItemIds()
    {
        if ($this->hasData('item_ids')) {
            $ids = $this->getData('item_ids');
            if (!is_array($ids)) {
                $ids = !empty($ids) ? explode(',', $ids) : array();
                $this->setData('item_ids', $ids);
            }
        } else {
        	$ids = array();
            foreach ($this->getItemsCollection() as $item) {
            	$ids[] = $item->getId();
            }
            $this->setData('item_ids', $ids);
        }
        return $this->getData('item_ids');
    }

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
                ->addStagingFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setStaging($this);
                }
            }
        }
        return $this->_items;
    }




    public function getMasterWebsiteIds()
    {
    	if ($this->hasData('master_website_ids')) {
            $ids = $this->getData('master_website_ids');
            if (!is_array($ids)) {
                $ids = !empty($ids) ? explode(',', $ids) : array();
                //$this->setData('master_website_ids', $ids);
                return $ids;
            }
        } else {
            $ids = array();
            foreach ($this->getWebsitesCollection() as $website) {
                $ids[] = $website->getMasterWebsiteId();
            }
            $this->setData('master_website_ids', $ids);
        }
        return $this->getData('master_website_ids');
    }

    public function getWebsiteIds()
    {
        if ($this->hasData('website_ids')) {
            $ids = $this->getData('website_ids');
            if (!is_array($ids)) {
                $ids = !empty($ids) ? explode(',', $ids) : array();
                //$this->setData('website_ids', $ids);
                return $ids;
            }
        } else {
            $ids = array();
            foreach ($this->getWebsitesCollection() as $website) {
                $ids[] = $website->getId();
            }
            $this->setData('website_ids', $ids);
        }
        return $this->getData('website_ids');
    }

    public function addWebsite(Enterprise_Staging_Model_Staging_Website $website)
    {
        $website->setStaging($this);
        if (!$website->getId()) {
            $this->getWebsitesCollection()->addItem($website);
        }
        return $this;
    }

    /**
     * Retrieve staging websites
     *
     * @return Varien_Data_Collection
     */
    public function getWebsitesCollection($returnAll = false)
    {
        if (is_null($this->_websites)) {
            $this->_websites = Mage::getResourceModel('enterprise_staging/staging_website_collection');
            if ($this->getId() || (!$this->getId() && !$returnAll)) {
                $this->_websites->addStagingFilter($this->getId());
            }
            if ($this->getId()) {
                foreach ($this->_websites as $website) {
                    $website->setStaging($this);
                }
            }
        }
        return $this->_websites;
    }

    public function getDatasetItemsCollection($ignoreBackendFlag = null)
    {
        if (is_null($this->_datasetItems)) {
            $this->_datasetItems = Mage::getResourceSingleton('enterprise_staging/dataset_item_collection')
                ->addBackendFilter($ignoreBackendFlag);
            if ($this->getDatasetId()) {
               $this->_datasetItems->addDatasetFilter($this->getDatasetId());
            }
        }
        return $this->_datasetItems;
    }

    /**
     * Retrieve dataset items array
     *
     * @return array
     */
    public function getDatasetItemIds()
    {
        $ids = array();
        foreach($this->getDatasetItemsCollection() as $item) {
            $ids[] = $item->getId();
        }
        return $ids;
    }

    public function create()
    {
        $this->getAdapterInstance(true)->create($this);

        return $this;
    }

    public function merge()
    {
        $this->getAdapterInstance(true)->merge($this);

        return $this;
    }

    public function backup()
    {
        $this->getAdapterInstance(true)->backup($this);

        return $this;
    }

    public function rollback()
    {
        $this->getAdapterInstance(true)->rollback($this);

        return $this;
    }

    protected function _checkState()
    {
        if (!$this->getId()) {
            $this->setIsNewStaging(true);
            $this->setState(Enterprise_Staging_Model_Staging_Config::STATE_NEW, Enterprise_Staging_Model_Staging_Config::STATUS_NEW);
            $this->setStatus(Enterprise_Staging_Model_Staging_Config::STATUS_NEW);
            return $this;
        }

        return $this;
    }

    /**
     * Declare staging state
     *
     * @param  string   $state
     * @param  string   $status
     * @param  string   $comment
     * @param  boolean  $isAdminNotified
     * @return object   Enterprise_Staging_Model_Staging
     */
    public function setState($state, $status = false, $comment = '', $log='', $isAdminNotified = false)
    {
        $this->setData('state', $state);
        if ($status) {
            if ($status === true) {
                $status = Enterprise_Staging_Model_Staging_Config::getStateDefaultStatus($state);
            }
        }
/*        if ($this->getEventCode()) {
            $eventName = Mage::helper('enterprise_staging')->__('Staging save');
            $this->addEvent($this->getEventCode(), $state, $status, $eventName, $comment, $log, $isAdminNotified);
        }*/
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
    public function addEvent($code, $state, $status, $name, $comment='', $log='', $isAdminNotified = false)
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
            ->setLog(Enterprise_Staging_Model_Log::buildLogReport($log))
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
     * Check staging type entities before save
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $this->_checkState();

    	$this->_protectFromNonAdmin();

        $this->cleanCache();

        $this->getTypeInstance(true)->beforeSave($this);

        return $this;
    }

    /**
     * Saving staging type entities
     *
     * @return Enterprise_Staging_Model_Staging
     */
    protected function _afterSave()
    {
        $this->getTypeInstance(true)->save($this);

        $this->getTypeInstance(true)->afterSave($this);

        parent::_afterSave();
    }

    /**
     * Check staging type entities before delete
     */
    protected function _beforeDelete()
    {
    	$this->_protectFromNonAdmin();

        $this->cleanCache();

        $this->getTypeInstance(true)->beforeDelete($this);

        parent::_beforeDelete();
    }

    /**
     * Deleting staging type entities
     *
     * @return Enterprise_Staging_Model_Staging
     */
    protected function _afterDelete()
    {
        $this->getTypeInstance(true)->delete($this);

        $this->getTypeInstance(true)->afterDelete($this);

        parent::_afterDelete();
    }

    /**
     * Clear cache related with staging id
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function cleanCache()
    {
        Mage::app()->cleanCache($this->_cacheTag . '_'.$this->getId());

        return $this;
    }

    public function getApplyDate()
    {
        return $this->_getData('apply_date');
    }

    public function getRollbackDate()
    {
        return $this->_getData('rollback_date');
    }

    /**
     * Check is staging configurable
     *
     * @return bool
     */
    public function isConfigurable()
    {
        return $this->getType() == Enterprise_Staging_Model_Staging_Type::TYPE_CONFIGURABLE;
    }

    public function getVisibleOnFront()
    {
        return Enterprise_Staging_Model_Config::checkCurrentVisibility($this);
    }



    /**
     * Retrieve type instance
     *
     * Type instance implement type depended logic
     *
     * @param bool $singleton
     * @return  Enterprise_Staging_Model_Staging_Type_Abstract
     */
    public function getTypeInstance($singleton = false)
    {
        if ($singleton === true) {
            if (is_null($this->_typeInstanceSingleton)) {
                $this->_typeInstanceSingleton = Enterprise_Staging_Model_Staging_Config::typeFactory($this, true);
            }
            return $this->_typeInstanceSingleton;
        }

        if ($this->_typeInstance === null) {
            $this->_typeInstance = Enterprise_Staging_Model_Staging_Config::typeFactory($this);
        }
        return $this->_typeInstance;
    }

    public function getMapperInstance($singleton = true)
    {
        if ($singleton === true) {
            if (is_null($this->_mapperInstanceSingleton)) {
                $this->_mapperInstanceSingleton = Enterprise_Staging_Model_Staging_Config::mapperFactory($this, true);
            }
            return $this->_mapperInstanceSingleton;
        }

        if ($this->_mapperInstance === null) {
            $this->_mapperInstance = Enterprise_Staging_Model_Staging_Config::mapperFactory($this);
        }
        return $this->_mapperInstance;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $singleton
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function getAdapterInstance($singleton = false)
    {
        if ($singleton === true) {
            if (is_null($this->_adapterInstanceSingleton)) {
                $this->_adapterInstanceSingleton = Enterprise_Staging_Model_Staging_Config::adapterFactory($this, true);
            }
            return $this->_adapterInstanceSingleton;
        }

        if ($this->_adapterInstance === null) {
            $this->_adapterInstance = Enterprise_Staging_Model_Staging_Config::adapterFactory($this);
        }
        return $this->_adapterInstance;
    }

    public function canSave()
    {
        if (!$this->getId()) {
            return false;
        }
        return true;
    }

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

    public function canMerge()
    {
        if (!$this->getId()) {
            return false;
        }
        if (($this->getStatus() == Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED)
            || ($this->getStatus() == Enterprise_Staging_Model_Staging_Config::STATUS_BROKEN)) {
            return false;
        }
        return true;
    }

    public function canRollback()
    {
        if (!$this->getId()) {
            return false;
        }
        if ($this->getStatus() == Enterprise_Staging_Model_Staging_Config::STATUS_MERGED) {
            return true;
        }
        return false;
    }

    public function updateAttribute($attribute, $value)
    {
        return $this->getResource()->updateAttribute($this, $attribute, $value);
    }

    public function saveEventHistory()
    {
        $this->getResource()->saveEvents($this);

        return $this;
    }
}
