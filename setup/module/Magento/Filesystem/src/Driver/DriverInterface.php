<?php
/**
 * Interface of Magento filesystem driver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Driver;

/**
 * Class Driver
 */
interface DriverInterface
{
    /**
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function isExists($path);

    /**
     * Check permissions for reading file or directory
     *
     * @param string $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isReadable($path);

    /**
     * Tells whether the filename is a regular directory
     *
     * @param string $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isDirectory($path);

    /**
     * Check if given path is writable
     *
     * @param string $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isWritable($path);

    /**
     * @param string $basePath
     * @param string $path
     * @return mixed
     */
    public function getAbsolutePath($basePath, $path);
}
