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


class Base implements \Magento\Filesystem\Driver
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
     * @param $path
     * @return bool
     * @throws FilesystemException
     */
    public function isExists($path)
    {
        clearstatcache();
        $result = @file_exists($path);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Error occurred during execution %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        clearstatcache();
        $result = @stat($path);
        if (!$result) {
            throw new FilesystemException(
                sprintf('Cannot gather stats! %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        clearstatcache();
        $result = @is_readable($path);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Error occurred during execution %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        clearstatcache();
        $result = @is_file($path);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Error occurred during execution %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        clearstatcache();
        $result = @is_dir($path);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Error occurred during execution %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
    }

    /**
     * Retrieve file contents from given path
     *
     * @param string $path
     * @return string
     * @throws FilesystemException
     */
    public function fileGetContents($path)
    {
        clearstatcache();
        $result = @file_get_contents($path);
        if (!$result) {
            throw new FilesystemException(
                sprintf('Cannot read contents from file "%s" %s',
                    $path,
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        clearstatcache();
        $result = @is_writable($path);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Error occurred during execution %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
    }

    /**
     * Returns parent directory's path
     *
     * @param string $path
     * @return string
     */
    public function getParentDirectory($path)
    {
        return dirname($path);
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
        $result = @mkdir($path, $permissions, true);
        if (!$result) {
            throw new FilesystemException(sprintf('Directory "%s" cannot be created %s',
                $path,
                $this->getWarningMessage()
            ));
        }
        return $result;
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
        $result = @rename($oldPath, $newPath);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The "%s" path cannot be renamed into "%s" %s',
                    $oldPath,
                    $newPath,
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @copy($source, $destination);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The file or directory "%s" cannot be copied to "%s" %s',
                    $source,
                    $destination,
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @unlink($path);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The file "%s" cannot be deleted %s',
                    $path,
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @rmdir($path);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The directory "%s" cannot be deleted %s',
                    $path,
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
                sprintf('Cannot change permissions for path "%s" %s',
                    $path,
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        if (!$modificationTime) {
            $result = @touch($path);
        } else {
            $result = @touch($path, $modificationTime);
        }
        if (!$result) {
            throw new FilesystemException(
                sprintf('The file or directory "%s" cannot be touched %s',
                    $path,
                    $this->getWarningMessage()
                ));
        }
        return $result;
    }

    /**
     * Open file in given path
     *
     * @param string $path
     * @param string $content
     * @param string|null $mode
     * @return int The number of bytes that were written.
     * @throws FilesystemException
     */
    public function filePutContents($path, $content, $mode = null)
    {
        $result = @file_put_contents($path, $content, $mode);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The specified "%s" file could not be written %s',
                    $path,
                    $this->getWarningMessage()
                ));
        }
        return $result;
    }

    /**
     * Open file
     *
     * @param string $path
     * @param string $mode
     * @return resource file
     * @throws FilesystemException
     */
    public function fileOpen($path, $mode)
    {
        $result = @fopen($path, $mode);
        if (!$result) {
            throw new FilesystemException(
                sprintf('File "%s" cannot be opened %s',
                    $path,
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @fread($resource, $length);
        if (!$result) {
            throw new FilesystemException(
                sprintf('File cannot be read %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @fgetcsv($resource, $length, $delimiter, $enclosure, $escape);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Wrong CSV handle %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @ftell($resource);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('Error occurred during execution %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @fseek($resource, $offset, $whence);
        if ($result === -1) {
            throw new FilesystemException(
                sprintf('Error occurred during execution of fileSeek %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
    }

    /**
     * Returns true if pointer at the end of file or in case of exception
     *
     * @param resource $resource
     * @return boolean
     */
    public function endOfFile($resource)
    {
        return feof($resource);
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
        $result = @fclose($resource);
        if (!$result) {
            throw new FilesystemException(
                sprintf('Error occurred during execution of fileClose %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @fwrite($resource, $data);
        if (!$result) {
            throw new FilesystemException(
                sprintf('Error occurred during execution of fileWrite %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @fputcsv($resource, $data, $delimiter, $enclosure);
        if (!$result) {
            throw new FilesystemException(
                sprintf('Error occurred during execution of filePutCsv %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @fflush($resource);
        if (!$result) {
            throw new FilesystemException(
                sprintf('Error occurred during execution of fileFlush %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
    }

    /**
     * Lock file in selected mode
     *
     * @param $resource
     * @param bool $exclusive
     * @return bool
     * @throws FilesystemException
     */
    public function fileLock($resource, $exclusive = true)
    {
        $lockMode = $exclusive ? LOCK_EX : LOCK_SH;
        $result = @flock($resource, $lockMode);
        if (!$result) {
            throw new FilesystemException(
                sprintf('Error occurred during execution of fileLock %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
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
        $result = @flock($resource, LOCK_UN);
        if (!$result) {
            throw new FilesystemException(
                sprintf('Error occurred during execution of fileUnlock %s',
                    $this->getWarningMessage()
                ));
        }
        return $result;
    }
}
