<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Locale;

class Resolver implements \Magento\Locale\ResolverInterface
{
    /**
     * Default locale code
     *
     * @var string
     */
    protected $_defaultLocale;

    /**
     * Locale object
     *
     * @var \Zend_Locale
     */
    protected $_locale;

    /**
     * Locale code
     *
     * @var string
     */
    protected $_localeCode;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\AppInterface
     */
    protected $_app;

    /**
     * Emulated locales stack
     *
     * @var array
     */
    protected $_emulatedLocales = array();

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\AppInterface $app
     * @param \Magento\Core\Helper\Translate $translate
     * @param null $locale
     */
    public function __construct(
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\AppInterface $app,
        \Magento\Core\Helper\Translate $translate,
        $locale = null
    ) {
        $this->_app = $app;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_translate = $translate;
        $this->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultLocale($locale)
    {
        $this->_defaultLocale = $locale;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        if (!$this->_defaultLocale) {
            $locale = $this->_coreStoreConfig->getConfig(\Magento\Locale\ResolverInterface::XML_PATH_DEFAULT_LOCALE);
            if (!$locale) {
                $locale = \Magento\Locale\ResolverInterface::DEFAULT_LOCALE;
            }
            $this->_defaultLocale = $locale;
        }
        return $this->_defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale = null)
    {
        if (($locale !== null) && is_string($locale)) {
            $this->_localeCode = $locale;
        } else {
            $this->_localeCode = $this->getDefaultLocale();
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            \Zend_Locale_Data::setCache($this->_app->getCache()->getLowLevelFrontend());
            $this->_locale = new \Zend_Locale($this->getLocaleCode());
        } elseif ($this->_locale->__toString() != $this->_localeCode) {
            $this->setLocale($this->_localeCode);
        }

        return $this->_locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        if ($this->_localeCode === null) {
            $this->setLocale();
        }
        return $this->_localeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocaleCode($code)
    {
        $this->_localeCode = $code;
        $this->_locale = null;
        return $this;
    }

    /**
     * Push current locale to stack and replace with locale from specified store
     *
     * @param int $storeId
     */
    public function emulate($storeId)
    {
        if ($storeId) {
            $this->_emulatedLocales[] = clone $this->getLocale();
            $this->_locale = new \Zend_Locale(
                $this->_coreStoreConfig->getConfig(
                    \Magento\Locale\ResolverInterface::XML_PATH_DEFAULT_LOCALE, $storeId
                ));
            $this->_localeCode = $this->_locale->toString();

            $this->_translate->initTranslate($this->_localeCode, true);
        } else {
            $this->_emulatedLocales[] = false;
        }
    }

    /**
     * Get last locale, used before last emulation
     */
    public function revert()
    {
        $locale = array_pop($this->_emulatedLocales);
        if ($locale) {
            $this->_locale = $locale;
            $this->_localeCode = $this->_locale->toString();

            $this->_translate->initTranslate($this->_localeCode, true);
        }
    }
}
