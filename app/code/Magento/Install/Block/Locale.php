<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Install localization block
 *
 * @category   Magento
 * @package    Magento_Install
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Block;

class Locale extends \Magento\Install\Block\AbstractBlock
{
    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'locale.phtml';

    /**
     * Locale code
     *
     * @var string
     */
    protected $_localeCode;

    /**
     * Set locale code
     *
     * @param $localeCode
     * @return \Magento\Install\Block\Locale
     */
    public function setLocaleCode($localeCode)
    {
        $this->_localeCode = $localeCode;
        return $this;
    }

    /**
     * Retrieve locale code
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->_localeCode;
    }

    /**
     * Retrieve locale object
     *
     * @return \Zend_Locale
     */
    public function getLocale()
    {
        $locale = $this->getData('locale');
        if (null === $locale) {
            $locale = $this->_locale->setLocaleCode(
                $this->getLocaleCode()
            )->getLocale();
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
        $html = $this->getLayout()->createBlock('Magento\View\Element\Html\Select')
            ->setName('config[locale]')
            ->setId('locale')
            ->setTitle(__('Locale'))
            ->setClass('required-entry')
            ->setValue($this->getLocale()->__toString())
            ->setOptions($this->_locale->getTranslatedOptionLocales())
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
        $html = $this->getLayout()->createBlock('Magento\View\Element\Html\Select')
            ->setName('config[timezone]')
            ->setId('timezone')
            ->setTitle(__('Time Zone'))
            ->setClass('required-entry')
            ->setValue($this->getTimezone())
            ->setOptions($this->_locale->getOptionTimezones())
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
        $timezone = $this->_session->getTimezone()
            ? $this->_session->getTimezone()
            : $this->_locale->getTimezone();
        if ($timezone == \Magento\Core\Model\LocaleInterface::DEFAULT_TIMEZONE) {
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
        $html = $this->getLayout()->createBlock('Magento\View\Element\Html\Select')
            ->setName('config[currency]')
            ->setId('currency')
            ->setTitle(__('Default Currency'))
            ->setClass('required-entry')
            ->setValue($this->getCurrency())
            ->setOptions($this->_locale->getOptionCurrencies())
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
        return $this->_session->getCurrency()
            ? $this->_session->getCurrency()
            : $this->_locale->getCurrency();
    }

    /**
     * @return \Magento\Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (null === $data) {
            $data = new \Magento\Object();
            $this->setData('form_data', $data);
        }
        return $data;
    }

}
