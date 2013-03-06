<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for fallback model.
 */
interface Mage_Core_Model_File_ResolverInterface
{
    /**
     * Get a usual file path (e.g. template)
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($file, $module = null);

    /**
     * Get locale file path (e.g. file with locale information)
     *
     * @param string $file
     * @return string
     */
    public function getLocaleFile($file);

    /**
     * Get view file path (e.g. javascript file)
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($file, $module = null);
}
