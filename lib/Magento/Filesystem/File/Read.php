<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\File;

use Magento\Filesystem\FilesystemException;

class Read implements ReadInterface
{
    /**
     * Full path to file
     *
     * @var string
     */
    protected $path;

    /**
     * Mode to open the file
     *
     * @var string
     */
    protected $mode = 'r';

    /**
     * Opened file resource
     *
     * @var resource
     */
    protected $resource;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->open();
    }

    /**
     * Open file
     *
     * @throws FilesystemException
     */
    protected function open()
    {
        $this->assertValid();
        $this->resource = fopen($this->path, $this->mode);
        if ($this->resource === false) {
            throw new FilesystemException(sprintf('The file "%s" cannot be opened', $this->path));
        }
    }

    /**
     * Assert file existence
     *
     * @throws FilesystemException
     */
    protected function assertValid()
    {
        clearstatcache();

        if (!file_exists($this->path)) {
            throw new FilesystemException(sprintf('The file "%s" doesn\'t exist', $this->path));
        }
    }

    /**
     * Reads the specified number of bytes from the current position.
     *
     * @param int $length The number of bytes to read
     * @return string
     */
    public function read($length)
    {
        return fread($this->resource, $length);
    }

    /**
     * Reads one CSV row from the file
     *
     * @param int $length [optional]
     * @param string $delimiter [optional]
     * @param string $enclosure [optional]
     * @param string $escape [optional]
     * @return array|bool|null
     */
    public function readCsv($length = 0, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        return fgetcsv($this->resource, $length, $delimiter, $enclosure, $escape);
    }

    /**
     * Returns the current position
     *
     * @return int
     */
    public function tell()
    {
        return ftell($this->resource);
    }

    /**
     * Seeks to the specified offset
     *
     * @param int $offset
     * @param int $whence
     * @return int
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->resource, $offset, $whence);
    }

    /**
     * Checks if the current position is the end-of-file
     *
     * @return bool
     */
    public function eof()
    {
        return feof($this->resource);
    }

    /**
     * Closes the file.
     *
     * @return bool
     */
    public function close()
    {
        return fclose($this->resource);
    }
}