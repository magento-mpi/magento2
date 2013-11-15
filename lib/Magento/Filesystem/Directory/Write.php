<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\Directory;

use Magento\Filesystem\FilesystemException;

class Write extends Read implements WriteInterface
{
    /**
     * @var int
     */
    protected $permissions;

    /**
     * Constructor
     *
     * @param string $path
     * @param \Magento\Filesystem\File\WriteFactory $fileFactory
     * @param \Magento\Filesystem\Driver $driver
     * @param $permissions
     */
    public function __construct
    (
        $path,
        \Magento\Filesystem\File\WriteFactory $fileFactory,
        \Magento\Filesystem\Driver $driver,
        $permissions
    )
    {
        $this->path = $path;
        $this->driver = $driver;
        $this->permissions = $permissions;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Check it directory is writable
     *
     * @throws \Magento\Filesystem\FilesystemException
     */
    protected function assertWritable($path)
    {
        $absolutePath = $this->getAbsolutePath($path);
        if ($this->isWritable($absolutePath) === false) {
            throw new FilesystemException(sprintf('The path "%s" is not writable', $absolutePath));
        }
    }

    /**
     * Create directory if it does not exists
     *
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function create($path)
    {
        $this->assertWritable($path);
        $absolutePath = $this->getAbsolutePath($path);
        if ($this->driver->isDirectory($absolutePath)) {
            return true;
        }
        return $this->driver->createDirectory($absolutePath, $this->permissions);
    }

    /**
     * Renames a source to into new name
     *
     * @param string $path
     * @param string $newPath
     * @param WriteInterface $targetDirectory
     * @return bool
     * @throws FilesystemException
     */
    public function rename($path, $newPath, WriteInterface $targetDirectory = null)
    {
        $this->assertExist($path);
        $targetDirectory = $targetDirectory ? : $this;
        if (!$targetDirectory->isExist(dirname($newPath))) {
            $targetDirectory->create(dirname($newPath));
        }
        $absolutePath = $this->getAbsolutePath($path);
        $absoluteNewPath = $targetDirectory->getAbsolutePath($newPath);
        return $this->driver->rename($absolutePath, $absoluteNewPath);
    }

    /**
     * Copy a source to into destination
     *
     * @param string $path
     * @param string $destination
     * @param WriteInterface $targetDirectory
     * @return bool
     * @throws FilesystemException
     */
    public function copy($path, $destination, WriteInterface $targetDirectory = null)
    {
        $this->assertExist($path);
        $targetDirectory = $targetDirectory ? : $this;
        if (!$targetDirectory->isExist(dirname($destination))) {
            $targetDirectory->create(dirname($destination));
        }

        $absolutePath = $this->getAbsolutePath($path);
        $absoluteDestinationPath = $targetDirectory->getAbsolutePath($destination);
        return $this->driver->copy($absolutePath, $absoluteDestinationPath);
    }

    /**
     * Delete given path
     *
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function delete($path = null)
    {
        $this->assertExist($path);
        $absolutePath = $this->getAbsolutePath($path);
        if ($this->driver->isFile($absolutePath)) {
            $this->driver->deleteFile($absolutePath);
        } else {
            foreach ($this->read($path) as $subPath) {
                $this->delete($subPath);
            }
            $this->driver->deleteDirectory($absolutePath);
        }
        return true;
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
        $this->assertExist($path);
        $absolutePath = $this->getAbsolutePath($path);
        return $this->driver->changePermissions($absolutePath,$permissions);
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
        $absolutePath = $this->getAbsolutePath($path);
        $folder = $this->driver->getParentDirectory($path);
        $this->create($folder);
        $this->assertWritable($folder);
        return $this->driver->touch($path, $modificationTime);
    }

    /**
     * Check if given path is writable
     *
     * @param null $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isWritable($path = null)
    {
        return $this->driver->isWritable($this->getAbsolutePath($path));
    }

    /**
     * Open file in given mode
     *
     * @param string $path
     * @param string $mode
     * @return \Magento\Filesystem\File\WriteInterface
     */
    public function openFile($path, $mode = 'w')
    {
        $absolutePath = $this->getAbsolutePath($path);
        $folder = $this->driver->getParentDirectory($absolutePath);
        $this->create($folder);
        $this->assertWritable($folder);
        return $this->fileFactory->create($absolutePath, $mode);
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
    public function writeFile($path, $content, $mode = null)
    {
        $absolutePath = $this->getAbsolutePath($path);
        $folder = $this->driver->getParentDirectory($absolutePath);
        $this->create($folder);
        $this->assertWritable($folder);
        return $this->driver->filePutContents($absolutePath, $content, $mode);
    }
}
