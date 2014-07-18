<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

interface WriteInterface extends ReadInterface
{
    /**
     * Check if given path is writable
     *
     * @param string $path [optional]
     * @return bool
     */
    public function isWritable($path = null);

    /**
     * Change permissions of given path
     *
     * @param string $path
     * @param int $permissions
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function changePermissions($path, $permissions);

    /**
     * Write contents to file in given mode
     *
     * @param string $path
     * @param string $content
     * @param string|null $mode
     * @return int The number of bytes that were written.
     */
    public function writeFile($path, $content, $mode = 'w+');
}
