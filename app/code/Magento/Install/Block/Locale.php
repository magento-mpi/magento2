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
     * @var \Magento\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magento\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Install\Model\Installer $installer
     * @param \Magento\Install\Model\Wizard $installWizard
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Locale\ListsInterface $localeLists
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Install\Model\Installer $installer,
        \Magento\Install\Model\Wizard $installWizard,
        \Magento\Framework\Session\Generic $session,
        \Magento\Locale\CurrencyInterface $localeCurrency,
        \Magento\Locale\ListsInterface $localeLists,
        \Magento\Locale\ResolverInterface $localeResolver,
        array $data = array()
    ) {
        $this->_localeLists = $localeLists;
        parent::__construct($context, $installer, $installWizard, $session, $data);
        $this->_localeCurrency = $localeCurrency;
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Set locale code
     *
     * @param string $localeCode
     * @return $this
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
     * @return \Magento\LocaleInterface
     */
    public function getLocale()
    {
        $locale = $this->getData('locale');
        if (null === $locale) {
            $locale = $this->_localeResolver->setLocaleCode($this->getLocaleCode())->getLocale();
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
        $html = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            'config[locale]'
        )->setId(
            'locale'
        )->setTitle(
            __('Locale')
        )->setClass(
            'required-entry'
        )->setValue(
            $this->getLocale()->__toString()
        )->setOptions(
            $this->_localeLists->getTranslatedOptionLocales()
        )->getHtml();
        return $html;
    }

    /**
     * Retrieve timezone dropdown HTML
     *
     * @return string
     */
    public function getTimezoneSelect()
    {
        $html = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            'config[timezone]'
        )->setId(
            'timezone'
        )->setTitle(
            __('Time Zone')
        )->setClass(
            'required-entry'
        )->setValue(
            $this->getTimezone()
        )->setOptions(
            $this->_localeLists->getOptionTimezones()
        )->getHtml();
        return $html;
    }

    /**
     * Retrieve timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        $timezone = $this->_session
            ->getTimezone() ? $this
            ->_session
            ->getTimezone() : $this
            ->_localeDate
            ->getDefaultTimezone();
        if ($timezone == \Magento\Stdlib\DateTime\TimezoneInterface::DEFAULT_TIMEZONE) {
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
        $html = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            'config[currency]'
        )->setId(
            'currency'
        )->setTitle(
            __('Default Currency')
        )->setClass(
            'required-entry'
        )->setValue(
            $this->getCurrency()
        )->setOptions(
            $this->_localeLists->getOptionCurrencies()
        )->getHtml();
        return $html;
    }

    /**
     * Retrieve currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->_session
            ->getCurrency() ? $this
            ->_session
            ->getCurrency() : $this
            ->_localeCurrency
            ->getDefaultCurrency();
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
