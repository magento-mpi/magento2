<?php
/**
 * Interface of Magento filesystem adapter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Filesystem_AdapterInterface
{
    /**
     * Checks the file existence.
     *
     * @param string $key
     * @return bool
     */
    public function exists($key);

    /**
     * Reads content of the file.
     *
     * @param string $key
     * @return string
     */
    public function read($key);

    /**
     * Writes content into the file.
     *
     * @param string $key
     * @param string $content
     * @return bool true if write was success
     */
    public function write($key, $content);

    /**
     * Deletes the file or directory recursively.
     *
     * @param string $key
     * @throws Magento_Filesystem_Exception
     */
    public function delete($key);

    /**
     * Renames the file.
     *
     * @param string $source
     * @param string $target
     * @return bool
     */
    public function rename($source, $target);

    /**
     * Changes permissions of filesystem key
     *
     * @param string $key
     * @param int $permissions
     * @param bool $recursively
     * @throws Magento_Filesystem_Exception
     */
    public function changePermissions($key, $permissions, $recursively);

    /**
     * Check if key is directory.
     *
     * @param string $key
     * @return bool
     */
    public function isDirectory($key);

    /**
     * Check if key is file.
     *
     * @param string $key
     * @return bool
     */
    public function isFile($key);

    /**
     * Check if file exists and is writable
     *
     * @param string $key
     * @return bool
     */
    public function isWritable($key);

    /**
     * Creates new directory
     *
     * @param string $key
     * @param int $mode
     * @throws Magento_Filesystem_Exception If cannot create directory
     */
    public function createDirectory($key, $mode);

    /**
     * Touches a file
     *
     * @param string $key
     * @throws Magento_Filesystem_Exception
     */
    public function touch($key);
}
