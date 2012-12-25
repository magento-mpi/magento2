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
     * Deletes the file.
     *
     * @param string $key
     * @return bool
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
     * Check if key is directory.
     *
     * @param string $key
     * @return bool
     */
    public function isDirectory($key);

    /**
     * Creates new directory
     *
     * @param string $key
     * @return bool
     */
    public function createDirectory($key);

    /*
     * Sets access and modification time of file
     *
     * @param string $key
     * @return bool
     */
    public function touch($key);
}
