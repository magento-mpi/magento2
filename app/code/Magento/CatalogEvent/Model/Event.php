<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Event model
 *
 * @method Magento_CatalogEvent_Model_Resource_Event _getResource()
 * @method Magento_CatalogEvent_Model_Resource_Event getResource()
 * @method int getCategoryId()
 * @method Magento_CatalogEvent_Model_Event setCategoryId(int $value)
 * @method string getDateStart()
 * @method Magento_CatalogEvent_Model_Event setDateStart(string $value)
 * @method string getDateEnd()
 * @method Magento_CatalogEvent_Model_Event setDateEnd(string $value)
 * @method int getDisplayState()
 * @method int getSortOrder()
 * @method Magento_CatalogEvent_Model_Event setSortOrder(int $value)
 */
class Magento_CatalogEvent_Model_Event extends Magento_Core_Model_Abstract
{
    const DISPLAY_CATEGORY_PAGE = 1;
    const DISPLAY_PRODUCT_PAGE  = 2;

    const STATUS_UPCOMING       = 'upcoming';
    const STATUS_OPEN           = 'open';
    const STATUS_CLOSED         = 'closed';

    const CACHE_TAG             = 'catalog_event';

    const IMAGE_PATH = 'enterprise/catalogevent';

    protected $_store = null;

    /**
     * Model cache tag for clear cache in after save and after delete
     */
    protected $_cacheTag        = self::CACHE_TAG;

    /**
     * Is model deleteable
     *
     * @var boolean
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var boolean
     */
    protected $_isReadonly = false;

    /**
     * Locale model
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Directory model
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_CatalogEvent_Model_Resource_Event $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_Dir $dir,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CatalogEvent_Model_Resource_Event $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_storeManager = $storeManager;
        $this->_dir = $dir;
        $this->_locale = $locale;
    }

    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento_CatalogEvent_Model_Resource_Event');
    }

    /**
     * Get cahce tags associated with object id.
     * Added category id tags support
     *
     * @return array
     */
    public function getCacheIdTags()
    {
        $tags = parent::getCacheIdTags();
        if ($this->getCategoryId()) {
            $tags[] = Magento_Catalog_Model_Category::CACHE_TAG . '_' . $this->getCategoryId();
        }
        return $tags;
    }

    /**
     * Apply event status
     *
     * @return Magento_CatalogEvent_Model_Event
     */
    protected function _afterLoad()
    {
        $this->_initDisplayStateArray();
        parent::_afterLoad();
        $this->getStatus();
        return $this;
    }

    /**
     * Initialize display state as array
     *
     * @return Magento_CatalogEvent_Model_Event
     */
    protected function _initDisplayStateArray()
    {
        $state = array();
        if ($this->canDisplayCategoryPage()) {
            $state[] = self::DISPLAY_CATEGORY_PAGE;
        }
        if ($this->canDisplayProductPage()) {
            $state[] = self::DISPLAY_PRODUCT_PAGE;
        }
        $this->setDisplayStateArray($state);
        return $this;
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return Magento_CatalogEvent_Model_Event
     */
    public function setStoreId($storeId = null)
    {
        $this->_store = $this->_storeManager->getStore($storeId);
        return $this;
    }

    /**
     * Retrieve store
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->_store === null) {
            $this->setStoreId();
        }

        return $this->_store;
    }

    /**
     * Set event image
     *
     * @param string|null|Magento_Core_Model_File_Uploader $value
     * @return Magento_CatalogEvent_Model_Event
     */
    public function setImage($value)
    {
        //in the current version should be used instance of Magento_Core_Model_File_Uploader
        if ($value instanceof Magento_File_Uploader) {
            $value->save($this->_dir->getDir(Magento_Core_Model_Dir::MEDIA) . DS . strtr(self::IMAGE_PATH, '/', DS));
            $value = $value->getUploadedFileName();
        }

        $this->setData('image', $value);
        return $this;
    }

    /**
     * Retrieve image url
     *
     * @return string|boolean
     */
    public function getImageUrl()
    {
        if ($this->getImage()) {
            return $this->_storeManager->getStore()->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_MEDIA) . '/'
                   . self::IMAGE_PATH . '/' . $this->getImage();
        }

        return false;
    }

    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * Set display state of catalog event
     *
     * @param int|array $state
     * @return Magento_CatalogEvent_Model_Event
     */
    public function setDisplayState($state)
    {
        if (is_array($state)) {
            $value = 0;
            foreach ($state as $_state) {
                $value ^= $_state;
            }
            $this->setData('display_state', $value);
        } else {
            $this->setData('display_state', $state);
        }
        return $this;
    }

    /**
     * Check display state for page type
     *
     * @param int $state
     * @return boolean
     */
    public function canDisplay($state)
    {
        return ((int) $this->getDisplayState() & $state) == $state;
    }

    /**
     * Check display state for product view page
     *
     * @return boolean
     */
    public function canDisplayProductPage()
    {
        return $this->canDisplay(self::DISPLAY_PRODUCT_PAGE);
    }

    /**
     * Check display state for category view page
     *
     * @return boolean
     */
    public function canDisplayCategoryPage()
    {
        return $this->canDisplay(self::DISPLAY_CATEGORY_PAGE);
    }

    /**
     * Apply event status by date
     *
     * @return Magento_CatalogEvent_Model_Event
     */
    public function applyStatusByDates()
    {
        if ($this->getDateStart() && $this->getDateEnd()) {
            $timeStart = $this->_getResource()->mktime($this->getDateStart()); // Date already in gmt, no conversion
            $timeEnd = $this->_getResource()->mktime($this->getDateEnd()); // Date already in gmt, no conversion
            $timeNow = gmdate('U');
            if ($timeStart <= $timeNow && $timeEnd >= $timeNow) {
                $this->setStatus(self::STATUS_OPEN);
            } elseif ($timeNow > $timeEnd) {
                $this->setStatus(self::STATUS_CLOSED);
            } else {
                $this->setStatus(self::STATUS_UPCOMING);
            }
        }
        return $this;
    }

    /**
     * Retrieve category ids with events
     *
     * @param int|string|Magento_Core_Model_Store $storeId
     * @return array
     */
    public function getCategoryIdsWithEvent($storeId = null)
    {
        return $this->_getResource()->getCategoryIdsWithEvent($storeId);
    }

    /**
     * Before save. Validation of data, and applying status, if needed.
     *
     * @return Magento_CatalogEvent_Model_Event
     * @throws Magento_Core_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $dateChanged = false;
        $fieldTitles = array('date_start' => __('Start Date') , 'date_end' => __('End Date'));
        foreach (array('date_start' , 'date_end') as $dateType) {
            $date = $this->getData($dateType);
            if (empty($date)) { // Date fields is required.
                throw new Magento_Core_Exception(__('%1 is required.', $fieldTitles[$dateType]));
            }
            if ($date != $this->getOrigData($dateType)) {
                $dateChanged = true;
            }
        }
        if ($dateChanged) {
            $this->applyStatusByDates();
        }

        return $this;
    }

    /**
     * Validates data for event
     * @returns boolean|array - returns true if validation passed successfully. Array with error
     * description otherwise
     */
    public function validate()
    {
        $dateStartUnixTime = strtotime($this->getData('date_start'));
        $dateEndUnixTime   = strtotime($this->getData('date_end'));
        $dateIsOk = $dateEndUnixTime > $dateStartUnixTime;
        if ($dateIsOk) {
            return true;
        }
        else {
            return array(__('Please make sure the end date follows the start date.'));
        }
    }

    /**
     * Checks if object can be deleted
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Sets flag for object if it can be deleted or not
     *
     * @param boolean $value
     * @return Magento_CatalogEvent_Model_Event
     */
    public function setIsDeleteable($value)
    {
        $this->_isDeleteable = (boolean) $value;
        return $this;
    }

    /**
     * Checks model is read only
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is read only flag
     *
     * @param boolean $value
     * @return Magento_CatalogEvent_Model_Event
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (boolean) $value;
        return $this;
    }

    /**
     * Get status column value
     * Set status column if it wasn't set
     *
     * @return string
     */
    public function getStatus()
    {
        if (!$this->hasData('status')) {
            $this->applyStatusByDates();
        }
        return $this->_getData('status');
    }

    /**
     * Converts passed start time value in sotre's
     * time zone to UTC time zone and sets it to object.
     *
     * @param string $value date time in store's time zone
     * @param mixed $store
     * @return Magento_CatalogEvent_Model_Event
     */
    public function setStoreDateStart($value, $store = null)
    {
        $date = $this->_locale->utcDate($store, $value, true, Magento_Date::DATETIME_INTERNAL_FORMAT);
        $this->setData('date_start', $date->toString(Magento_Date::DATETIME_INTERNAL_FORMAT));
        return $this;
    }

    /**
     * Converts passed end time value in sotre's
     * time zone to UTC time zone and sets it to object.
     *
     * @param string $value date time in store's time zone
     * @param mixed $store
     * @return Magento_CatalogEvent_Model_Event
     */
    public function setStoreDateEnd($value, $store = null)
    {
        $date = $this->_locale->utcDate($store, $value, true, Magento_Date::DATETIME_INTERNAL_FORMAT);
        $this->setData('date_end', $date->toString(Magento_Date::DATETIME_INTERNAL_FORMAT));
        return $this;
    }

    /**
     * Gets start time from object, converts it from UTC time zone
     * to store's time zone. Result is formatted by internal format
     * and in time zone of current store or passed through parameter.
     *
     * @param mixed $store
     * @return string
     */
    public function getStoreDateStart($store = null)
    {
        if ($this->getData('date_start')) {
            $value = $this->getResource()->mktime($this->getData('date_start'));
            if (!$value) {
                return null;
            }
            $date = $this->_locale->storeDate($store, $value, true);
            return $date->toString(Magento_Date::DATETIME_INTERNAL_FORMAT);
        }

        return $this->getData('date_start');
    }

    /**
     * Gets end time from object, converts it from UTC time zone
     * to store's time zone. Result is formatted by internal format
     * and in time zone of current store or passed through parameter.
     *
     * @param mixed $store
     * @return string
     */
    public function getStoreDateEnd($store = null)
    {
        if ($this->getData('date_end')) {
            $value = $this->getResource()->mktime($this->getData('date_end'));
            if (!$value) {
                return null;
            }
            $date = $this->_locale->storeDate($store, $value, true);
            return $date->toString(Magento_Date::DATETIME_INTERNAL_FORMAT);
        }

        return $this->getData('date_end');
    }
}
