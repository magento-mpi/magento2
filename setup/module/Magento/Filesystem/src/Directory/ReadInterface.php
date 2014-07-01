<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

interface ReadInterface
{
    /**
     * Get absolute path
     *
     * @param string $path [optional]
     * @return string
     */
    public function getAbsolutePath($path = null);

    /**
     * Check a file or directory exists
     *
     * @param string $path [optional]
     * @return bool
     */
    public function isExist($path = null);

    /**
     * Check permissions for reading file or directory
     *
     * @param string $path
     * @return bool
     */
    public function isReadable($path = null);

    /**
     * Check whether given path is directory
     *
     * @param string $path
     * @return bool
     */
    public function isDirectory($path = null);
}
