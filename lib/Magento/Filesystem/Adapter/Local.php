<?php
/**
 * Adapter for local filesystem
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Adapter_Local implements
    Magento_Filesystem_AdapterInterface,
    Magento_Filesystem_Stream_FactoryInterface
{
    /**
     * Checks the file existence.
     *
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        return file_exists($key);
    }

    /**
     * Reads content of the file.
     *
     * @param string $key
     * @return string
     */
    public function read($key)
    {
        return file_get_contents($key);
    }

    /**
     * Writes content into the file.
     *
     * @param string $key
     * @param string $content
     * @return bool true if write was success
     */
    public function write($key, $content)
    {
        return (bool)file_put_contents($key, $content);
    }

    /**
     * Deletes the file.
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return unlink($key);
    }

    /**
     * Renames the file.
     *
     * @param string $source
     * @param string $target
     * @return bool
     */
    public function rename($source, $target)
    {
        return rename($source, $target);
    }

    /**
     * Changes mode of filesystem key
     *
     * @param string $key
     * @param int $mode
     * @throws Magento_Filesystem_Exception
     */
    public function changeMode($key, $mode)
    {
        if (!@chmod($key, $mode, true)) {
            throw new Magento_Filesystem_Exception(sprintf('Failed to change mode of %s', $key));
        }
    }

    /**
     * Check if key is a directory.
     *
     * @param string $key
     * @return bool
     */
    public function isDirectory($key)
    {
        return is_dir($key);
    }

    /**
     * Check if key is a file.
     *
     * @param string $key
     * @return bool
     */
    public function isFile($key)
    {
        return is_file($key);
    }

    /**
     * Creates new directory
     *
     * @param string $key
     * @param int $mode
     * @throws Magento_Filesystem_Exception
     */
    public function createDirectory($key, $mode)
    {
        if (!@mkdir($key, $mode, true)) {
            throw new Magento_Filesystem_Exception(sprintf('Failed to create %s', $key));
        }
    }

    /**
     * Touches a file
     *
     * @param string $key
     * @throws Magento_Filesystem_Exception
     */
    public function touch($key)
    {
        if (!@touch($key)) {
            throw new Magento_Filesystem_Exception(sprintf('Failed to touch %s', $key));
        }
    }

    /**
     * Create stream object
     *
     * @param string $path
     * @return Magento_Filesystem_Stream_Local
     */
    public function createStream($path)
    {
        return new Magento_Filesystem_Stream_Local($path);
    }
}
