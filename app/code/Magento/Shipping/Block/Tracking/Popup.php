<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Shipping_Block_Tracking_Popup extends Magento_Core_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        /* @var $info Magento_Shipping_Model_Info */
        $info = $this->_coreRegistry->registry('current_shipping_info');

        return $info->getTrackingInfo();
    }

    /**
     * Format given date and time in current locale without changing timezone
     *
     * @param string $date
     * @param string $time
     * @return string
     */
    public function formatDeliveryDateTime($date, $time)
    {
        return $this->formatDeliveryDate($date) . ' ' . $this->formatDeliveryTime($time);
    }

    /**
     * Format given date in current locale without changing timezone
     *
     * @param string $date
     * @return string
     */
    public function formatDeliveryDate($date)
    {
        /* @var $locale Magento_Core_Model_LocaleInterface */
        $locale = Mage::app()->getLocale();
        $format = $locale->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
        return $locale->date(strtotime($date), Zend_Date::TIMESTAMP, null, false)
            ->toString($format);
    }

    /**
     * Format given time [+ date] in current locale without changing timezone
     *
     * @param string $time
     * @param string $date
     * @return string
     */
    public function formatDeliveryTime($time, $date = null)
    {
        if (!empty($date)) {
            $time = $date . ' ' . $time;
        }

        /* @var $locale Magento_Core_Model_LocaleInterface */
        $locale = Mage::app()->getLocale();

        $format = $locale->getTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        return $locale->date(strtotime($time), Zend_Date::TIMESTAMP, null, false)
            ->toString($format);
    }

    /**
     * Is 'contact us' option enabled?
     *
     * @return boolean
     */
    public function getContactUsEnabled()
    {
        return (bool) $this->_storeConfig->getConfig('contacts/contacts/enabled');
    }

    public function getStoreSupportEmail()
    {
        return $this->_storeConfig->getConfig('trans_email/ident_support/email');
    }

    public function getContactUs()
    {
        return $this->getUrl('contacts');
    }

}
