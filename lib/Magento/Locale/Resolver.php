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
     * @var \Magento\LocaleInterface
     */
    protected $_locale;

    /**
     * Locale code
     *
     * @var string
     */
    protected $_localeCode;

    /**
     * @var \Magento\BaseScopeResolverInterface
     */
    protected $_scopeResolver;

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
     * @var \Magento\LocaleFactory
     */
    protected $_localeFactory;

    /**
     * @param \Magento\BaseScopeResolverInterface $scopeResolver
     * @param \Magento\AppInterface $app
     * @param \Magento\LocaleFactory $localeFactory
     * @param string $defaultLocalePath
     * @param mixed $locale
     */
    public function __construct(
        \Magento\BaseScopeResolverInterface $scopeResolver,
        \Magento\AppInterface $app,
        \Magento\LocaleFactory $localeFactory,
        $defaultLocalePath,
        $locale = null
    ) {
        $this->_app = $app;
        $this->_scopeResolver = $scopeResolver;
        $this->_localeFactory = $localeFactory;
        $this->_defaultLocalePath = $defaultLocalePath;
        $this->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocalePath()
    {
        return $this->_defaultLocalePath;
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
            $locale = $this->_getConfig($this->getDefaultLocalePath());
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
            $this->_locale = $this->_localeFactory->create(array('locale' => $this->getLocaleCode()));
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
     * {@inheritdoc}
     */
    public function emulate($scopeId)
    {
        $result = null;
        if ($scopeId) {
            $this->_emulatedLocales[] = clone $this->getLocale();
            $this->_locale = $this->_localeFactory->create(array(
                'locale' => $this->_getConfig($this->getDefaultLocalePath(), $scopeId)
            ));
            $this->_localeCode = $this->_locale->toString();
            $result = $this->_localeCode;
        } else {
            $this->_emulatedLocales[] = false;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function revert()
    {
        $result = null;
        $locale = array_pop($this->_emulatedLocales);
        if ($locale) {
            $this->_locale = $locale;
            $this->_localeCode = $this->_locale->toString();
            $result = $this->_localeCode;
        }
        return $result;
    }

    /**
     * Retrieve config value by path
     *
     * @param string $path
     * @param null|string|bool|int|\Magento\Url\ScopeInterface $scope
     */
    protected function _getConfig($path, $scope = null)
    {
        $this->_scopeResolver->getScope($scope)->getConfig($path);
    }
}
