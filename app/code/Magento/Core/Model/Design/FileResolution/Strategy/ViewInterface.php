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
 * Interface for 'view' file resolution strategy
 */
interface Magento_Core_Model_Design_FileResolution_Strategy_ViewInterface
{
    /**
     * Get theme file name (e.g. a javascript file)
     *
     * @param string $area
     * @param Magento_Core_Model_Theme $themeModel
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($area, Magento_Core_Model_Theme $themeModel, $locale, $file, $module = null);
}
