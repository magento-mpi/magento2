<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\File;

use Magento\Filesystem\FilesystemException;

class Write extends Read implements WriteInterface
{
    /**
     * @param string $path
     * @param string $mode
     */
    public function __construct($path, $mode)
    {
        $this->mode = $mode;
        parent::__construct($path);
    }

    /**
     * Assert file existence for proper mode
     *
     * @throws \Magento\Filesystem\FilesystemException
     */
    protected function assertValid()
    {
        clearstatcache();

        $fileExists = file_exists($this->path);
        if (!$fileExists && preg_match('/r/', $this->mode)) {
            throw new FilesystemException(sprintf('The file "%s" doesn\'t exist', $this->path));
        } elseif ($fileExists && preg_match('/x/', $this->mode)) {
            throw new FilesystemException(sprintf('The file "%s" already exists', $this->path));
        }
    }

    /**
     * Writes the data to file.
     *
     * @param string $data
     * @return int
     * @throws FilesystemException
     */
    public function write($data)
    {
        $result = fwrite($this->resource, $data);
        if ($result === false) {
            throw new FilesystemException(sprintf('Cannot write to the "%s" file', $this->path));
        }
        return $result;
    }

    /**
     * Writes one CSV row to the file.
     *
     * @param array $data
     * @param string $delimiter
     * @param string $enclosure
     * @return int
     * @throws FilesystemException
     */
    public function writeCsv(array $data, $delimiter = ',', $enclosure = '"')
    {
        $result = fputcsv($this->resource, $data, $delimiter, $enclosure);
        if ($result === false) {
            throw new FilesystemException(sprintf('Cannot write to the "%s" file', $this->path));
        }
        return $result;
    }

    /**
     * Flushes the output.
     *
     * @return bool
     * @throws FilesystemException
     */
    public function flush()
    {
        $result = fflush($this->resource);
        if ($result === false) {
            throw new FilesystemException(sprintf('Cannot flush the "%s" file', $this->path));
        }
        return $result;
    }

    /**
     * Portable advisory file locking
     *
     * @param bool $exclusive
     * @return bool
     */
    public function lock($exclusive = true)
    {
        $lock = $exclusive ? LOCK_EX : LOCK_SH;
        return flock($this->resource, $lock);
    }

    /**
     * File unlocking
     *
     * @return bool
     */
    public function unlock()
    {
        return flock($this->resource, LOCK_UN);
    }

    /**
     * Closes the file.
     *
     * @return bool
     * @throws FilesystemException
     */
    public function close()
    {
        $result = fclose($this->resource);
        if ($result === false) {
            throw new FilesystemException(sprintf('Cannot close the "%s" file', $this->path));
        }
        return $result;
    }
}