<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\File;

use Magento\Filesystem\FilesystemException;
use Magento\Webapi\Exception;

class Write extends Read implements WriteInterface
{
    /**
     * Constructor
     *
     * @param $path
     * @param \Magento\Filesystem\Driver $driver
     * @param $mode
     */
    public function __construct(
        $path,
        \Magento\Filesystem\Driver $driver,
        $mode
    )
    {
        parent::__construct($path, $driver);
        $this->mode = $mode;
    }

    /**
     * Assert file existence for proper mode
     *
     * @throws \Magento\Filesystem\FilesystemException
     */
    protected function assertValid()
    {
        $fileExists = $this->driver->isExists($this->path);
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
        try {
            return $this->driver->fileWrite($this->resource, $data);
        } catch (FilesystemException $e) {
            throw new FilesystemException(
                sprintf('Cannot write to the "%s" file. %s',
                    $this->path,
                    $e->getMessage()
                ));
        }
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
        try {
            return $this->driver->filePutCsv($this->resource, $data, $delimiter, $enclosure);
        } catch (FilesystemException $e) {
            throw new FilesystemException(
                sprintf('Cannot write to the "%s" file. %s',
                    $this->path,
                    $e->getMessage()
                ));
        }
    }

    /**
     * Flushes the output.
     *
     * @return bool
     * @throws FilesystemException
     */
    public function flush()
    {
        try {
            return $this->driver->fileFlush($this->resource);
        } catch (FilesystemException $e) {
            throw new FilesystemException(
                sprintf('Cannot flush the "%s" file. %s',
                    $this->path,
                    $e->getMessage()
                ));
        }
    }

    /**
     * Portable advisory file locking
     *
     * @param bool $exclusive
     * @return bool
     */
    public function lock($exclusive = true)
    {
        return $this->driver->fileLock($exclusive);
    }

    /**
     * File unlocking
     *
     * @return bool
     */
    public function unlock()
    {
        return $this->driver->fileUnlock($this->resource);
    }
}