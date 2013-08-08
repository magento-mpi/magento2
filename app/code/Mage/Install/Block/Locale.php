<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Install localization block
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_Locale extends Mage_Install_Block_Abstract
{

    protected $_template = 'locale.phtml';

    /**
     * Retrieve locale object
     *
     * @return Zend_Locale
     */
    public function getLocale()
    {
        $locale = $this->getData('locale');
        if (is_null($locale)) {
            $locale = Mage::app()->getLocale()->getLocale();
            $this->setData('locale', $locale);
        }
        return $locale;
    }

    /**
     * Retrieve locale data post url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getCurrentStep()->getNextUrl();
        //return $this->getUrl('*/*/localePost');
    }

    /**
     * Retrieve locale change url
     *
     * @return string
     */
    public function getChangeUrl()
    {
        return $this->getUrl('*/*/localeChange');
    }

    /**
     * Retrieve locale dropdown HTML
     *
     * @return string
     */
    public function getLocaleSelect()
    {
        $html = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
            ->setName('config[locale]')
            ->setId('locale')
            ->setTitle(__('Locale'))
            ->setClass('required-entry')
            ->setValue($this->getLocale()->__toString())
            ->setOptions(Mage::app()->getLocale()->getTranslatedOptionLocales())
            ->getHtml();
        return $html;
    }

    /**
     * Retrieve timezone dropdown HTML
     *
     * @return string
     */
    public function getTimezoneSelect()
    {
        $html = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
            ->setName('config[timezone]')
            ->setId('timezone')
            ->setTitle(__('Time Zone'))
            ->setClass('required-entry')
            ->setValue($this->getTimezone())
            ->setOptions(Mage::app()->getLocale()->getOptionTimezones())
            ->getHtml();
        return $html;
    }

    /**
     * Retrieve timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        $timezone = Mage::getSingleton('Mage_Install_Model_Session')->getTimezone()
            ? Mage::getSingleton('Mage_Install_Model_Session')->getTimezone()
            : Mage::app()->getLocale()->getTimezone();
        if ($timezone == Mage::DEFAULT_TIMEZONE) {
            $timezone = 'America/Los_Angeles';
        }
        return $timezone;
    }

    /**
     * Retrieve currency dropdown html
     *
     * @return string
     */
    public function getCurrencySelect()
    {
        $html = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
            ->setName('config[currency]')
            ->setId('currency')
            ->setTitle(__('Default Currency'))
            ->setClass('required-entry')
            ->setValue($this->getCurrency())
            ->setOptions(Mage::app()->getLocale()->getOptionCurrencies())
            ->getHtml();
        return $html;
    }

    /**
     * Retrieve currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return Mage::getSingleton('Mage_Install_Model_Session')->getCurrency()
            ? Mage::getSingleton('Mage_Install_Model_Session')->getCurrency()
            : Mage::app()->getLocale()->getCurrency();
    }

    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = new Varien_Object();
            $this->setData('form_data', $data);
        }
        return $data;
    }

}
