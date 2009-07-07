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
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Catalog Event model
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Model_Event extends Mage_Core_Model_Abstract
{
    const DISPLAY_CATEGORY_PAGE = 1;
    const DISPLAY_PRODUCT_PAGE  = 2;

    const STATUS_UPCOMING       = 'upcoming';
    const STATUS_OPEN           = 'open';
    const STATUS_CLOSED         = 'closed';

    const XML_PATH_DEFAULT_TIMEZONE = 'general/locale/timezone';

    const IMAGE_PATH = 'enterprise/catalogevent';

    protected $_store = null;


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
     * Intialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('enterprise_catalogevent/event');
    }

    protected function _afterLoad()
    {
        $this->_initDisplayStateArray();
        return parent::_afterLoad();
    }

    /**
     * Initialize display state as array
     *
     * @return Enterprise_CatalogEvent_Model_Event
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
     * @return Enterprise_CatalogEvent_Model_Event
     */
    public function setStoreId($storeId = null)
    {
        $this->_store = Mage::app()->getStore($storeId);
        return $this;
    }

    /**
     * Retrive store
     *
     * @return Mage_Core_Model_Store
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
     * @param string|null|Varien_File_Uploader $value
     * @return Enterprise_CatalogEvent_Model_Event
     */
    public function setImage($value)
    {
        if ($value instanceof Varien_File_Uploader) {
            $value->save(Mage::getBaseDir('media') . DS
                         . strtr(self::IMAGE_PATH, '/', DS));

            $value = $value->getUploadedFileName();
        }

        $this->setData('image', $value);
        return $this;
    }

    /**
     * Retreive image url
     *
     * @return string|boolean
     */
    public function getImageUrl()
    {
        if ($this->getImage()) {
            return Mage::getBaseUrl('media') . '/'
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
     * @return Enterprise_CatalogEvent_Model_Event
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
     * @return Enterprise_CatalogEvent_Model_Event
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
     * Retreive category ids with events
     *
     * @param int|string|Mage_Core_Model_Store $storeId
     * @return array
     */
    public function getCategoryIdsWithEvent($storeId = null)
    {
        return $this->_getResource()->getCategoryIdsWithEvent($storeId);
    }

    /**
     * Before save. Validation of data, and applying status, if needed.
     *
     * @return Enterprise_CatalogEvent_Model_Event
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $dateChanged = false;
        $fieldTitles = array('date_start' => Mage::helper('enterprise_catalogevent')->__('Start Date') , 'date_end' => Mage::helper('enterprise_catalogevent')->__('End Date'));
        foreach (array('date_start' , 'date_end') as $dateType) {
            $date = $this->getData($dateType);
            if (empty($date)) { // Date fields is required.
                Mage::throwException(Mage::helper('enterprise_catalogevent')->__('%s is required.', $fieldTitles[$dateType]));
            }
            if ($date != $this->getOrigData($dateType)) {
                $dateChanged = true;
                try {
                    $this->setData($dateType, $this->_convertDateTime($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                } catch (Exception $e) {
                    Mage::throwException(Mage::helper('enterprise_catalogevent')->__('%s has invalid date format.', $fieldTitles[$dateType]));
                }
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
        $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT;
        $dateStartUnixTime = strtotime($this->_convertDateTime($this->getData('date_start'), $format));
        $dateEndUnixTime   = strtotime($this->_convertDateTime($this->getData('date_end'), $format));
        $dateIsOk = $dateEndUnixTime > $dateStartUnixTime;
        if ($dateIsOk) {
            return true;
        }
        else {
            return array(Mage::helper('enterprise_catalogevent')->__("End Date should be greater than Start Date"));
        }
    }

    /**
     * Converts given date to internal date format in UTC
     *
     * @param  string $dateTime
     * @param  string $format
     * @return string
     */
    protected function _convertDateTime($dateTime, $format)
    {
        $date = new Zend_Date(Mage::app()->getLocale()->getLocale());
        $date->setTimezone(Mage::app()->getStore()->getConfig(self::XML_PATH_DEFAULT_TIMEZONE));
        $format = Mage::app()->getLocale()->getDateTimeFormat($format);
        $date->set($dateTime, $format);
        $date->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }


    /**
     * Checks model is deleteable
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deleteable flag
     *
     * @param boolean $value
     * @return Enterprise_CatalogEvent_Model_Event
     */
    public function setIsDeleteable($value)
    {
        $this->_isDeleteable = (boolean) $value;
        return $this;
    }

    /**
     * Checks model is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is readonly flag
     *
     * @param boolean $value
     * @return Enterprise_CatalogEvent_Model_Event
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (boolean) $value;
        return $this;
    }

    /**
     * Providing check of status and updates it (with saving into DB) if status changed 
     */
    public function updateStatus()
    {
        $originalStatus = $this->getStatus();
        if ($originalStatus == self::STATUS_OPEN || $originalStatus == self::STATUS_UPCOMING) {
            $this->applyStatusByDates();
            if ($this->getStatus() != $originalStatus) {
                $this->save();
            }
        }
    }

    /**
     * Overriden load method
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function load($id, $field=null)
    {
        $event = parent::load($id, $field);
        $this->updateStatus();
        return $event;
    }

}