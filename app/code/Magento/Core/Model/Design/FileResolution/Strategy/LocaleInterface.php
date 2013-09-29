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
namespace Magento\Core\Model\Design\FileResolution\Strategy;

interface LocaleInterface
{
    /**
     * Get locale file name (e.g. file with translations)
     *
     * @param string $area
     * @param \Magento\Core\Model\Theme $themeModel
     * @param string $locale
     * @param string $file
     * @return string
     */
    public function getLocaleFile($area, \Magento\Core\Model\Theme $themeModel, $locale, $file);
}
