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
     * @param string $path
     * @return string mixed
     */
    public function getAbsolutePath($path);

    /**
     * Retrieve list of all entities in given path
     *
     * @param string $path
     * @return array
     */
    public function read($path);

    /**
     * Search all entries for given regex pattern
     *
     * @param string $pattern
     * @return array
     */
    public function search($pattern);

    /**
     * Check a file or directory exists
     *
     * @param string $path
     * @return bool
     */
    public function isExist($path);

    /**
     * Gathers the statistics of the given path
     *
     * @param string $path
     * @return array
     */
    public function stat($path);

    /**
     * Check permissions for reading file or directory
     *
     * @param string $path
     * @return bool
     */
    public function isReadable($path);

    /**
     * Check whether given path is file
     *
     * @param string $path
     * @return bool
     */
    public function isFile($path);

    /**
     * Check whether given path is directory
     *
     * @param string $path
     * @return bool
     */
    public function isDirectory($path);

    /**
     * Open file in read mode
     *
     * @param string $path
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function openFile($path);

    /**
     * Retrieve file contents from given path
     *
     * @param string $path
     * @return string
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function readFile($path);
}
