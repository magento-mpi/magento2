<?php
/**
 * Abstract config source model for available locales
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Saas_Backend_Model_Config_Source_AbstractLocale implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Allowed locale codes
     *
     * @var array
     */
    protected $_allowedLocaleCodes = array();

    /**
     * Locale model
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var array|bool
     */
    protected $_options = false;

    /**
     * Create instance of current class with appropriate parameters
     *
     * @param array $allowedLocaleCodes
     * @param Magento_Core_Model_LocaleInterface $locale
     */
    public function __construct(Magento_Core_Model_LocaleInterface $locale, array $allowedLocaleCodes = array())
    {
        $this->_locale = $locale;
        $this->_allowedLocaleCodes = $allowedLocaleCodes;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (false === $this->_options) {
            $this->_options = $this->_filterLocales($this->_getLocales());
        }
        return $this->_options;
    }

    /**
     * Return locales array
     *
     * @return array
     */
    abstract protected function _getLocales();

    /**
     * Filter locales
     *
     * @param array $locales
     * @return array
     */
    protected function _filterLocales($locales)
    {
        if ($this->_allowedLocaleCodes) {
            $allowedLocaleCodes = $this->_allowedLocaleCodes;

            return array_filter($locales, function ($element) use ($allowedLocaleCodes) {
                return in_array($element['value'], $allowedLocaleCodes) ? true : false;
            });
        }
        return $locales;
    }
}
