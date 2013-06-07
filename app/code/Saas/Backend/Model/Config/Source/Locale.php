<?php
/**
 * Config source model for available locales
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backend_Model_Config_Source_Locale extends Saas_Backend_Model_Config_Source_AbstractLocale
{
    /**
     * Return locales array
     *
     * @return array
     */
    protected function _getLocales()
    {
        return $this->_locale->getOptionLocales();
    }
}
