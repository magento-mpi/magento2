<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Abstract extends Varien_Object
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_abstract';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'object';

    /**
     * Resource model instance
     *
     * @var Mage_Core_Model_Resource_Db_Abstract
     */
    protected $_resource;

    /**
     * Resource collection
     *
     * @var Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected $_resourceCollection;
    /**
     * Name of the resource model
     *
     * @var string
     */
    protected $_resourceName;

    /**
     * Name of the resource collection model
     *
     * @var string
     */
    protected $_resourceCollectionName;

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * When you use true - all cache will be clean
     *
     * @var string || true
     */
    protected $_cacheTag = false;

    /**
     * Flag which can stop data saving after before save
     * Can be used for next sequence: we check data in _beforeSave, if data are
     * not valid - we can set this flag to false value and save process will be stopped
     *
     * @var bool
     */
    protected $_dataSaveAllowed = true;

    /**
     * Flag which allow detect object state: is it new object (without id) or existing one (with id)
     *
     * @var bool
     */
    protected $_isObjectNew = null;

    /**
     * Application Event Dispatcher
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventDispatcher;

    /**
     * Application Cache Manager
     *
     * @var Mage_Core_Model_Cache
     */
    protected $_cacheManager;

    /**
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param array $data
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_cacheManager = $cacheManager;
        $this->_resource = $resource;
        $this->_resourceCollection = $resourceCollection;

        parent::__construct($data);
        $this->_construct();
    }

    /**
     * Model construct that should be used for object initialization
     */
    protected function _construct()
    {
    }

    /**
     * Standard model initialization
     *
     * @param string $resourceModel
     * @param string $idFieldName
     * @return Mage_Core_Model_Abstract
     */
    protected function _init($resourceModel)
    {
        $this->_setResourceModel($resourceModel);
        $this->_idFieldName = $this->_getResource()->getIdFieldName();
    }

    /**
     * Set resource names
     *
     * If collection name is ommited, resource name will be used with _collection appended
     *
     * @param string $resourceName
     * @param string|null $resourceCollectionName
     */
    protected function _setResourceModel($resourceName, $resourceCollectionName = null)
    {
        $this->_resourceName = $resourceName;
        if (is_null($resourceCollectionName)) {
            $resourceCollectionName = $resourceName . '_Collection';
        }
        $this->_resourceCollectionName = $resourceCollectionName;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _getResource()
    {
        if (empty($this->_resourceName) && empty($this->_resource)) {
            Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Resource is not set.'));
        }

        return $this->_resource ? $this->_resource : Mage::getResourceSingleton($this->_resourceName);
    }

    /**
     * Retrieve model resource name
     *
     * @return string
     */
    public function getResourceName()
    {
        return ($this->_resource) ? get_class($this->_resource) : ($this->_resourceName ? $this->_resourceName : null);
    }

    /**
     * Get collection instance
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getResourceCollection()
    {
        if (empty($this->_resourceCollectionName) &&  empty($this->_resourceCollection)) {
            Mage::throwException(
                Mage::helper('Mage_Core_Helper_Data')->__('Model collection resource name is not defined.')
            );
        }
        return $this->_resourceCollection ?
            $this->_resourceCollection :
            Mage::getResourceModel($this->_resourceCollectionName, array('resource' => $this->_getResource()));
    }

    /**
     * Retrieve collection instance
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getCollection()
    {
        return $this->getResourceCollection();
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function load($id, $field=null)
    {
        $this->_beforeLoad($id, $field);
        $this->_getResource()->load($this, $id, $field);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    /**
     * Get array of objects transfered to default events processing
     *
     * @return array
     */
    protected function _getEventData()
    {
        return array(
            'data_object'       => $this,
            $this->_eventObject => $this,
        );
    }

    /**
     * Processing object before load data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeLoad($id, $field = null)
    {
        $params = array('object' => $this, 'field' => $field, 'value'=> $id);
        $this->_eventDispatcher->dispatch('model_load_before', $params);
        $params = array_merge($params, $this->_getEventData());
        $this->_eventDispatcher->dispatch($this->_eventPrefix.'_load_before', $params);
        return $this;
    }

    /**
     * Processing object after load data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterLoad()
    {
        $this->_eventDispatcher->dispatch('model_load_after', array('object'=>$this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix.'_load_after', $this->_getEventData());
        return $this;
    }

    /**
     * Object after load processing. Implemented as public interface for supporting objects after load in collections
     *
     * @return Mage_Core_Model_Abstract
     */
    public function afterLoad()
    {
        $this->getResource()->afterLoad($this);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Check whether model has changed data.
     * Can be overloaded in child classes to perform advanced check whether model needs to be saved
     * e.g. usign resouceModel->hasDataChanged() or any other technique
     *
     * @return boolean
     */
    protected function _hasModelChanged()
    {
        return $this->hasDataChanges();
    }

    /**
     * Save object data
     *
     * @return Mage_Core_Model_Abstract
     */
    public function save()
    {
        /**
         * Direct deleted items to delete method
         */
        if ($this->isDeleted()) {
            return $this->delete();
        }
        if (!$this->_hasModelChanged()) {
            return $this;
        }
        $this->_getResource()->beginTransaction();
        try {
            $this->_beforeSave();
            if ($this->_dataSaveAllowed) {
                $this->_getResource()->save($this);
                $this->_afterSave();
            }
            $this->_getResource()->addCommitCallback(array($this, 'afterCommitCallback'))
                ->commit();
            $this->_hasDataChanges = false;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            $this->_hasDataChanges = true;
            throw $e;
        }
        return $this;
    }

    /**
     * Callback function which called after transaction commit in resource model
     *
     * @return Mage_Core_Model_Abstract
     */
    public function afterCommitCallback()
    {
        $this->_eventDispatcher->dispatch('model_save_commit_after', array('object'=>$this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix.'_save_commit_after', $this->_getEventData());
        return $this;
    }

    /**
     * Check object state (true - if it is object without id on object just created)
     * This method can help detect if object just created in _afterSave method
     * problem is what in after save onject has id and we can't detect what object was
     * created in this transaction
     *
     * @param bool $flag
     * @return bool
     */
    public function isObjectNew($flag=null)
    {
        if ($flag !== null) {
            $this->_isObjectNew = $flag;
        }
        if ($this->_isObjectNew !== null) {
            return $this->_isObjectNew;
        }
        return !(bool)$this->getId();
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->isObjectNew(true);
        }
        $this->_eventDispatcher->dispatch('model_save_before', array('object'=>$this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix.'_save_before', $this->_getEventData());
        return $this;
    }

    /**
     * Get list of cache tags applied to model object.
     * Return false if cache tags are not supported by model
     *
     * @return array | false
     */
    public function getCacheTags()
    {
        $tags = false;
        if ($this->_cacheTag) {
            if ($this->_cacheTag === true) {
                $tags = array();
            } else {
                if (is_array($this->_cacheTag)) {
                    $tags = $this->_cacheTag;
                } else {
                    $tags = array($this->_cacheTag);
                }
                $idTags = $this->getCacheIdTags();
                if ($idTags) {
                    $tags = array_merge($tags, $idTags);
                }
            }
        }
        return $tags;
    }

    /**
     * Get cahce tags associated with object id
     *
     * @return array
     */
    public function getCacheIdTags()
    {
        $tags = false;
        if ($this->getId() && $this->_cacheTag) {
            $tags = array();
            if (is_array($this->_cacheTag)) {
                foreach ($this->_cacheTag as $_tag) {
                    $tags[] = $_tag.'_'.$this->getId();
                }
            } else {
                $tags[] = $this->_cacheTag.'_'.$this->getId();
            }
        }
        return $tags;
    }

    /**
     * Remove model onject related cache
     *
     * @return Mage_Core_Model_Abstract
     */
    public function cleanModelCache()
    {
        $tags = $this->getCacheTags();
        if ($tags !== false) {
            $this->_cacheManager->clean($tags);
        }
        return $this;
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        $this->cleanModelCache();
        $this->_eventDispatcher->dispatch('model_save_after', array('object'=>$this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix.'_save_after', $this->_getEventData());
        return $this;
    }

    /**
     * Delete object from database
     *
     * @return Mage_ Core_Model_Abstract
     */
    public function delete()
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_beforeDelete();
            $this->_getResource()->delete($this);
            $this->_afterDelete();

            $this->_getResource()->commit();
            $this->_afterDeleteCommit();
        }
        catch (Exception $e){
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Processing object before delete datac
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeDelete()
    {
        $this->_eventDispatcher->dispatch('model_delete_before', array('object'=>$this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix.'_delete_before', $this->_getEventData());
        $this->cleanModelCache();
        return $this;
    }

    /**
     * Safeguard func that will check, if we are in admin area
     *
     * @throws Mage_Core_Exception
     */
    protected function _protectFromNonAdmin()
    {
        if (Mage::registry('isSecureArea')) {
            return;
        }
        if (!Mage::app()->getStore()->isAdmin()) {
            Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Cannot complete this operation from non-admin area.'));
        }
    }

    /**
     * Processing object after delete data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterDelete()
    {
        $this->_eventDispatcher->dispatch('model_delete_after', array('object'=>$this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix.'_delete_after', $this->_getEventData());
        return $this;
    }

    /**
     * Processing manipulation after main transaction commit
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterDeleteCommit()
    {
         $this->_eventDispatcher->dispatch('model_delete_commit_after', array('object'=>$this));
         $this->_eventDispatcher->dispatch($this->_eventPrefix.'_delete_commit_after', $this->_getEventData());
         return $this;
    }

    /**
     * Retrieve model resource
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function getResource()
    {
        return $this->_getResource();
    }

    /**
     * Retreive entity id
     *
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->_getData('entity_id');
    }

    /**
     * Clearing object for correct deleting by garbage collector
     *
     * @return Mage_Core_Model_Abstract
     */
    final public function clearInstance()
    {
        $this->_clearReferences();
        $this->_eventDispatcher->dispatch($this->_eventPrefix.'_clear', $this->_getEventData());
        $this->_clearData();
        return $this;
    }

    /**
     * Clearing cyclic references
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _clearReferences()
    {
        return $this;
    }

    /**
     * Clearing object's data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _clearData()
    {
        return $this;
    }

}
