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
 * Interface for 'file' file resolution strategy
 */
interface Magento_Core_Model_Design_FileResolution_Strategy_FileInterface
{
    /**
     * Get a usual file path (e.g. template)
     *
     * @param string $area
     * @param Magento_Core_Model_Theme $themeModel
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($area, Magento_Core_Model_Theme $themeModel, $file, $module = null);
}
