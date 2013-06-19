<?php
/**
 * Config source model for available translated locales
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backend_Model_Config_Source_Locale_Translated extends Saas_Backend_Model_Config_Source_AbstractLocale
{
    /**
     * Return locales array
     *
     * @return array
     */
    protected function _getLocales()
    {
        return $this->_locale->getTranslatedOptionLocales();
    }
}
