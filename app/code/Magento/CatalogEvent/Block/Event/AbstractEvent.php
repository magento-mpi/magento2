<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event Abstract event block
 */

namespace Magento\CatalogEvent\Block\Event;

abstract class AbstractEvent extends \Magento\Core\Block\Template
{
    /**
     * Event statuses titles
     *
     * @var array
     */
    protected $_statuses;

    /**
     * Locale model
     *
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * Construct
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\LocaleInterface $locale,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);

        $this->_locale = $locale;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_statuses = array(
            \Magento\CatalogEvent\Model\Event::STATUS_UPCOMING => __('Coming Soon'),
            \Magento\CatalogEvent\Model\Event::STATUS_OPEN     => __('Sale Ends In'),
            \Magento\CatalogEvent\Model\Event::STATUS_CLOSED   => __('Closed'),
        );
    }

    /**
     * Return catalog event status text
     *
     * @param \Magento\CatalogEvent\Model\Event $event
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
     * @param \Magento\CatalogEvent\Model\Event $event
     * @param string $format
     * @return string
     */
    public function getEventTime($type, $event, $format = null)
    {
        if ($format === null) {
            $format = $this->_locale->getTimeFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);
        }

        return $this->_getEventDate($type, $event, $format);
    }

    /**
     * Return event formatted date
     *
     * @param string $type (start, end)
     * @param \Magento\CatalogEvent\Model\Event $event
     * @param string $format
     * @return string
     */
    public function getEventDate($type, $event, $format = null)
    {
        if ($format === null) {
            $format = $this->_locale->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);
        }

        return $this->_getEventDate($type, $event, $format);

    }

    /**
     * Return event formatted datetime
     *
     * @param string $type (start, end)
     * @param \Magento\CatalogEvent\Model\Event $event
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
     * @param \Magento\CatalogEvent\Model\Event $event
     * @param string $format
     * @return string
     */
    protected function _getEventDate($type, $event, $format)
    {
        $date = new \Zend_Date($this->_locale->getLocale());
        // changing timezone to UTC
        $date->setTimezone(\Magento\Core\Model\LocaleInterface::DEFAULT_TIMEZONE);

        $dateString = $event->getData('date_' . $type);
        $date->set($dateString, \Magento\Date::DATETIME_INTERNAL_FORMAT);

        $timezone = $this->_storeConfig->getConfig(\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_TIMEZONE);
        if ($timezone) {
            // changing timezone to default store timezone
            $date->setTimezone($timezone);
        }
        return $date->toString($format);
    }

    /**
     * Return event time to close in seconds
     *
     * @param \Magento\CatalogEvent\Model\Event $event
     * @return int
     */
    public function getSecondsToClose($event)
    {
        $endTime = strtotime($event->getDateEnd());
        $currentTime = gmdate('U');

        return $endTime - $currentTime;
    }

    /**
     * Check availability to display event block
     *
     * @return boolean
     */
    abstract public function canDisplay();
}
