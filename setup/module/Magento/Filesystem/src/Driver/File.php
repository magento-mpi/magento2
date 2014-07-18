<?php
/**
 * Origin filesystem driver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Driver;

use Magento\Filesystem\FilesystemException;

class File implements DriverInterface
{
    /**
     * Returns last warning message string
     *
     * @return string
     */
    protected function getWarningMessage()
    {
        $warning = error_get_last();
        if ($warning && $warning['type'] == E_WARNING) {
            return 'Warning!' . $warning['message'];
        }
        return null;
    }

    /**
     * Is file or directory exist in file system
     *
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function isExists($path)
    {
        clearstatcache();
        $result = @file_exists($path);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Error occurred during execution %s', $this->getWarningMessage())
            );
        }
        return $result;
    }

    /**
     * Check permissions for reading file or directory
     *
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function isReadable($path)
    {
        clearstatcache();
        $result = @is_readable($path);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Error occurred during execution %s', $this->getWarningMessage())
            );
        }
        return $result;
    }

    /**
     * Tells whether the filename is a regular directory
     *
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function isDirectory($path)
    {
        clearstatcache();
        $result = @is_dir($path);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Error occurred during execution %s', $this->getWarningMessage())
            );
        }
        return $result;
    }

    /**
     * Check if given path is writable
     *
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function isWritable($path)
    {
        clearstatcache();
        $result = @is_writable($path);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Error occurred during execution %s', $this->getWarningMessage())
            );
        }
        return $result;
    }

    /**
     * @param string $basePath
     * @param string $path
     * @return string
     */
    public function getAbsolutePath($basePath, $path)
    {
        return $basePath . ltrim($this->fixSeparator($path), '/');
    }

    /**
     * Fixes path separator
     * Utility method.
     *
     * @param string $path
     * @return string
     */
    protected function fixSeparator($path)
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * Change permissions of given path
     *
     * @param string $path
     * @param int $permissions
     * @return bool
     * @throws FilesystemException
     */
    public function changePermissions($path, $permissions)
    {
        $result = @chmod($path, $permissions);
        if (!$result) {
            throw new FilesystemException(
                sprintf('Cannot change permissions for path "%s" %s', $path, $this->getWarningMessage())
            );
        }
        return $result;
    }
}
