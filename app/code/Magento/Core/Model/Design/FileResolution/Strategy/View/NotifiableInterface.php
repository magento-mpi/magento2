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
 * Interface for a view strategy to be notifiable, that file location has changed
 */
namespace Magento\Core\Model\Design\FileResolution\Strategy\View;

interface NotifiableInterface
{
    /**
     * Notify the strategy, that file has changed its location, and next time should be resolved to this
     * new location.
     *
     * @param string $area
     * @param \Magento\Core\Model\Theme $themeModel
     * @param string $locale
     * @param string|null $module
     * @param string $file
     * @param string $newFilePath
     * @return Magento_Core_Model_FileResolution_Fallback_CachingProxy
     */
    public function setViewFilePathToMap($area, \Magento\Core\Model\Theme $themeModel, $locale, $module, $file,
        $newFilePath
    );
}
