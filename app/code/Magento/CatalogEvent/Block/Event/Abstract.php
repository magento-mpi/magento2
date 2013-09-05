<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Event Abstract event block
 *
 * @category   Magento
 * @package    Magento_CatalogEvent
 */

abstract class Magento_CatalogEvent_Block_Event_Abstract extends Magento_Core_Block_Template
{
    /**
     * Event statuses titles
     *
     * @var array
     */
    protected $_statuses;

    protected function _construct()
    {
        parent::_construct();
        $this->_statuses = array(
            Magento_CatalogEvent_Model_Event::STATUS_UPCOMING => __('Coming Soon'),
            Magento_CatalogEvent_Model_Event::STATUS_OPEN     => __('Sale Ends In'),
            Magento_CatalogEvent_Model_Event::STATUS_CLOSED   => __('Closed'),
        );
    }

    /**
     * Return catalog event status text
     *
     * @param Magento_CatalogEvent_Model_Event $event
     * @return string
     */
    public function getStatusText($event)
    {
        if (isset($this->_statuses[$event->getStatus()])) {
            return $this->_statuses[$event->getStatus()];
        }

        return '';
    }

    /**
     * Return event formatted time
     *
     * @param string $type (start, end)
     * @param Magento_CatalogEvent_Model_Event $event
     * @param string $format
     * @return string
     */
    public function getEventTime($type, $event, $format = null)
    {
        if ($format === null) {
            $format = $this->_getLocale()->getTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
        }

        return $this->_getEventDate($type, $event, $format);
    }

    /**
     * Return event formatted date
     *
     * @param string $type (start, end)
     * @param Magento_CatalogEvent_Model_Event $event
     * @param string $format
     * @return string
     */
    public function getEventDate($type, $event, $format = null)
    {
        if ($format === null) {
            $format = $this->_getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
        }

        return $this->_getEventDate($type, $event, $format);

    }

    /**
     * Return event formatted datetime
     *
     * @param string $type (start, end)
     * @param Magento_CatalogEvent_Model_Event $event
     * @param string $format
     * @return string
     */
    public function getEventDateTime($type, $event)
    {
        return $this->getEventDate($type, $event) . ' ' . $this->getEventDate($type, $event);
    }

    /**
     * Return event date by in store timezone, with specified format
     *
     * @param string $type (start, end)
     * @param Magento_CatalogEvent_Model_Event $event
     * @param string $format
     * @return string
     */
    protected function _getEventDate($type, $event, $format)
    {
        $date = new Zend_Date($this->_getLocale()->getLocale());
        // changing timezone to UTC
        $date->setTimezone(Mage::DEFAULT_TIMEZONE);

        $dateString = $event->getData('date_' . $type);
        $date->set($dateString, \Magento\Date::DATETIME_INTERNAL_FORMAT);

        if (($timezone = Mage::app()->getStore()->getConfig(Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_TIMEZONE))) {
            // changing timezone to default store timezone
            $date->setTimezone($timezone);
        }
        return $date->toString($format);
    }

    /**
     * Return event time to close in seconds
     *
     * @param Magento_CatalogEvent_Model_Event $event
     * @return int
     */
    public function getSecondsToClose($event)
    {
        $endTime = strtotime($event->getDateEnd());
        $currentTime = gmdate('U');

        return $endTime - $currentTime;
    }

    /**
     * Retrieve current locale
     *
     * @return Magento_Core_Model_LocaleInterface
     */
    protected function _getLocale()
    {
        return Mage::app()->getLocale();
    }

    /**
     * Check availability to display event block
     *
     * @return boolean
     */
    abstract public function canDisplay();

}
