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
namespace Magento\Core\Model\Design\FileResolution\Strategy;

interface FileInterface
{
    /**
     * Get a usual file path (e.g. template)
     *
     * @param string $area
     * @param \Magento\View\Design\ThemeInterface $themeModel
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($area, \Magento\View\Design\ThemeInterface $themeModel, $file, $module = null);
}
