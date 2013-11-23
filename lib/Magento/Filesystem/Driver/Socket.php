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

use Magento\Filesystem\Driver\Base;
use Magento\Filesystem\FilesystemException;


class Socket extends Base
{
    protected $handles = array();

    /**
     * @param $path
     * @return bool
     * @throws FilesystemException
     */
    public function isExists($path)
    {
        $headers = array_change_key_case(get_headers($path, 1), CASE_LOWER);

        $status = $headers[0];

        if (strpos($status, '200 OK') === false) {
            $result = false;
        } else {
            $result = true;
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
        $headers = array_change_key_case(get_headers($path, 1), CASE_LOWER);

        $result = array(
            'dev' => 0,
            'ino' => 0,
            'mode' => 0,
            'nlink' => 0,
            'uid' => 0,
            'gid' => 0,
            'rdev' => 0,
            'atime' => 0,
            'ctime' => 0,
            'blksize' => 0,
            'blocks' => 0,
            'size' => isset($headers['content-length']) ? $headers['content-length'] : 0,
            'type' => isset($headers['content-type']) ? $headers['content-type'] : '',
            'mtime' => isset($headers['last-modified']) ? $headers['last-modified'] : 0,
            'disposition' => isset($headers['content-disposition']) ? $headers['content-disposition'] : null
        );
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
        $urlProp = parse_url($path);
        if (!isset($urlProp['scheme']) || strtolower($urlProp['scheme'] != 'http')) {
            throw new FilesystemException(__('Please correct the download URL scheme.'));
        }

        if (!isset($urlProp['host'])) {
            throw new FilesystemException(__('Please correct the download URL host.'));
        }

        $hostname = $urlProp['host'];
        $port = 80;
        if (isset($urlProp['port'])) {
            $port = (int)$urlProp['port'];
        }

        $path = '/';
        if (isset($urlProp['path'])) {
            $path = $urlProp['path'];
        }

        $query = '';
        if (isset($urlProp['query'])) {
            $query = '?' . $urlProp['query'];
        }

        try {
            $result = fsockopen($hostname, $port, $errorNumber, $errorMessage);
        } catch (\Exception $e) {
            throw new FilesystemException($e->getMessage());
        }

        if ($result === false) {
            throw new FilesystemException(
                __('Something went wrong connecting to the host. Error#%1 - %2.', $errorNumber, $errorMessage)
            );
        }

        $headers = 'GET ' . $path . $query . ' HTTP/1.0' . "\r\n"
            . 'Host: ' . $hostname . "\r\n"
            . 'User-Agent: Magento ver/' . \Magento\Core\Model\App::VERSION . "\r\n"
            . 'Connection: close' . "\r\n"
            . "\r\n";

        fwrite($result, $headers);

        // trim headers
        while (!feof($result)) {
            $str = fgets($result, 1024);
            if ($str == "\r\n") {
                break;
            }
        }

        return $result;
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
        $result = @stream_get_line($resource, $length, $ending);

        return $result;
    }
}
