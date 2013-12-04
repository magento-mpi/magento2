<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\Protocol;
use Magento\Filesystem\FilesystemException;

/**
 * Class File
 *
 * @package Magento\Filesystem\Protocol
 */
class Php implements \Magento\Filesystem\DriverInterface
{
    /**
     * @var \Magento\Filesystem\DriverInterface
     */
    protected $driver;

    /**
     * @param \Magento\Filesystem\DriverInterface $driver
     */
    public function __construct(\Magento\Filesystem\DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function fileGetContents($path)
    {
        return $this->driver->fileGetContents($path);
    }

    /**
     *
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function isExists($path)
    {
        return true;
    }

    /**
     * Gathers the statistics of the given path
     *
     * @param string $path
     * @return array
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function stat($path)
    {
        // TODO: Implement stat() method.
    }

    /**
     * Check permissions for reading file or directory
     *
     * @param string $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isReadable($path)
    {
        // TODO: Implement isReadable() method.
    }

    /**
     * Tells whether the filename is a regular file
     *
     * @param string $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isFile($path)
    {
        // TODO: Implement isFile() method.
    }

    /**
     * Tells whether the filename is a regular directory
     *
     * @param string $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isDirectory($path)
    {
        // TODO: Implement isDirectory() method.
    }

    /**
     * Check if given path is writable
     *
     * @param string $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isWritable($path)
    {
        // TODO: Implement isWritable() method.
    }

    /**
     * Returns parent directory's path
     *
     * @param string $path
     * @return string
     */
    public function getParentDirectory($path)
    {
        // TODO: Implement getParentDirectory() method.
    }

    /**
     * Create directory
     *
     * @param string $path
     * @param int $permissions
     * @return bool
     * @throws FilesystemException
     */
    public function createDirectory($path, $permissions)
    {
        // TODO: Implement createDirectory() method.
    }

    /**
     * Renames a file or directory
     *
     * @param string $oldPath
     * @param string $newPath
     * @return bool
     * @throws FilesystemException
     */
    public function rename($oldPath, $newPath)
    {
        // TODO: Implement rename() method.
    }

    /**
     * Copy source into destination
     *
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws FilesystemException
     */
    public function copy($source, $destination)
    {
        // TODO: Implement copy() method.
    }

    /**
     * Delete file
     *
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function deleteFile($path)
    {
        // TODO: Implement deleteFile() method.
    }

    /**
     * Delete directory
     *
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function deleteDirectory($path)
    {
        // TODO: Implement deleteDirectory() method.
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
        // TODO: Implement changePermissions() method.
    }

    /**
     * Sets access and modification time of file.
     *
     * @param string $path
     * @param int|null $modificationTime
     * @return bool
     * @throws FilesystemException
     */
    public function touch($path, $modificationTime = null)
    {
        // TODO: Implement touch() method.
    }

    /**
     * Put contents into given file
     *
     * @param string $path
     * @param string $content
     * @param string|null $mode
     * @return int The number of bytes that were written.
     * @throws FilesystemException
     */
    public function filePutContents($path, $content, $mode = null)
    {
        // TODO: Implement filePutContents() method.
    }

    /**
     * Open file
     *
     * @param string $path
     * @param string $mode
     * @return resource
     * @throws FilesystemException
     */
    public function fileOpen($path, $mode)
    {
        return $this->driver->fileOpen($path, $mode);
    }

    /**
     * Reads the line content from file pointer (with specified number of bytes from the current position).
     *
     * @param resource $resource
     * @param int $length
     * @param string $ending [optional]
     * @return string
     * @throws FilesystemException
     */
    public function fileReadLine($resource, $length, $ending = null)
    {
        // TODO: Implement fileReadLine() method.
    }

    /**
     * Reads the specified number of bytes from the current position.
     *
     * @param resource $resource
     * @param int $length
     * @return string
     * @throws FilesystemException
     */
    public function fileRead($resource, $length)
    {
        // TODO: Implement fileRead() method.
    }

    /**
     * Reads one CSV row from the file
     *
     * @param resource $resource
     * @param int $length [optional]
     * @param string $delimiter [optional]
     * @param string $enclosure [optional]
     * @param string $escape [optional]
     * @return array|bool|null
     * @throws FilesystemException
     */
    public function fileGetCsv($resource, $length = 0, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        // TODO: Implement fileGetCsv() method.
    }

    /**
     * Returns position of read/write pointer
     *
     * @param resource $resource
     * @return int
     * @throws FilesystemException
     */
    public function fileTell($resource)
    {
        // TODO: Implement fileTell() method.
    }

    /**
     * Seeks to the specified offset
     *
     * @param resource $resource
     * @param int $offset
     * @param int $whence
     * @return int
     * @throws FilesystemException
     */
    public function fileSeek($resource, $offset, $whence = SEEK_SET)
    {
        // TODO: Implement fileSeek() method.
    }

    /**
     * Returns true if pointer at the end of file or in case of exception
     *
     * @param resource $resource
     * @return boolean
     */
    public function endOfFile($resource)
    {
        // TODO: Implement endOfFile() method.
    }

    /**
     * Close file
     *
     * @param resource $resource
     * @return boolean
     * @throws FilesystemException
     */
    public function fileClose($resource)
    {
        // TODO: Implement fileClose() method.
    }

    /**
     * Writes data to file
     *
     * @param resource $resource
     * @param string $data
     * @return int
     * @throws FilesystemException
     */
    public function fileWrite($resource, $data)
    {
        // TODO: Implement fileWrite() method.
    }

    /**
     * Writes one CSV row to the file.
     *
     * @param resource $resource
     * @param array $data
     * @param string $delimiter
     * @param string $enclosure
     * @return int
     * @throws FilesystemException
     */
    public function filePutCsv($resource, array $data, $delimiter = ',', $enclosure = '"')
    {
        // TODO: Implement filePutCsv() method.
    }

    /**
     * Flushes the output
     *
     * @param resource $resource
     * @return bool
     * @throws FilesystemException
     */
    public function fileFlush($resource)
    {
        // TODO: Implement fileFlush() method.
    }

    /**
     * Lock file in selected mode
     *
     * @param $resource
     * @param int $lockMode
     * @return bool
     * @throws FilesystemException
     */
    public function fileLock($resource, $lockMode = LOCK_EX)
    {
        // TODO: Implement fileLock() method.
    }

    /**
     * Unlock file
     *
     * @param $resource
     * @return bool
     * @throws FilesystemException
     */
    public function fileUnlock($resource)
    {
        // TODO: Implement fileUnlock() method.
    }
}
