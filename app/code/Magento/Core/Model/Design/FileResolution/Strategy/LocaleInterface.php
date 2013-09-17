<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for 'locale' file resolution strategy
 */
interface Magento_Core_Model_Design_FileResolution_Strategy_LocaleInterface
{
    /**
     * Get locale file name (e.g. file with translations)
     *
     * @param string $area
     * @param Magento_Core_Model_Theme $themeModel
     * @param string $locale
     * @param string $file
     * @return string
     */
    public function getLocaleFile($area, Magento_Core_Model_Theme $themeModel, $locale, $file);
}
