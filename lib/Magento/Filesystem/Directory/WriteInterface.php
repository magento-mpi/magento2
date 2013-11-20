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
     * Create directory if it does not exists
     *
     * @param string $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function create($path);

    /**
     * Renames a source to into new name
     *
     * @param string $path
     * @param string $newPath
     * @param WriteInterface $targetDirectory
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function rename($path, $newPath, WriteInterface $targetDirectory = null);

    /**
     * Copy a file
     *
     * @param string $path
     * @param string $destination
     * @param WriteInterface $targetDirectory
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function copy($path, $destination, WriteInterface $targetDirectory = null);

    /**
     * Delete given path
     *
     * @param string $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function delete($path);

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
     * Sets access and modification time of file.
     *
     * @param string $path
     * @param int|null $modificationTime
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function touch($path, $modificationTime = null);

    /**
     * Check if given path is writable
     *
     * @param string $path
     * @return bool
     */
    public function isWritable($path);

    /**
     * Open file in given mode
     *
     * @param string $path
     * @param string|null $mode
     * @return \Magento\Filesystem\File\WriteInterface
     */
    public function openFile($path, $mode = 'w');

    /**
     * Open file in given path
     *
     * @param string $path
     * @param string $content
     * @param string|null $mode
     * @return int The number of bytes that were written.
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function writeFile($path, $content, $mode = null);
}