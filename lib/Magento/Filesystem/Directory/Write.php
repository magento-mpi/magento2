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
     * Is directory creation
     *
     * @var bool
     */
    protected $allowCreateDirs;

    /**
     * Permissions for new directories and files
     *
     * @var int
     */
    protected $permissions = 0777;

    /**
     * Constructor
     *
     * @param array $config
     * @param \Magento\Filesystem\File\WriteFactory $fileFactory
     * @param \Magento\Filesystem\DriverInterface $driver
     */
    public function __construct
    (
        array $config,
        \Magento\Filesystem\File\WriteFactory $fileFactory,
        \Magento\Filesystem\DriverInterface $driver
    ) {
        $this->setProperties($config);
        $this->fileFactory = $fileFactory;
        $this->driver = $driver;
    }

    /**
     * Set properties from config
     *
     * @param array $config
     * @throws \Magento\Filesystem\FilesystemException
     */
    protected function setProperties(array $config)
    {
        parent::setProperties($config);
        if (isset($config['permissions'])) {
            $this->permissions = $config['permissions'];
        }
        if (isset($config['allow_create_dirs'])) {
            $this->allowCreateDirs = (bool) $config['allow_create_dirs'];
        }
    }

    /**
     * Check if directory is writable
     *
     * @throws \Magento\Filesystem\FilesystemException
     */
    protected function assertWritable($path)
    {
        if ($this->isWritable($path) === false) {
            $path = $this->getAbsolutePath($this->path, $path);
            throw new FilesystemException(sprintf('The path "%s" is not writable', $path));
        }
    }

    /**
     * Check if given path is exists and is file
     *
     * @param string $path
     * @throws \Magento\Filesystem\FilesystemException
     */
    protected function assertIsFile($path)
    {
        clearstatcache();
        $absolutePath = $this->driver->getAbsolutePath($this->path, $path);
        if (!$this->driver->isFile($absolutePath)) {
            throw new FilesystemException(sprintf('The "%s" file doesn\'t exist or not a file', $absolutePath));
        }
    }

    /**
     * Create directory if it does not exists
     *
     * @param string $path
     * @return bool
     * @throws FilesystemException
     */
    public function create($path = null)
    {
        $absolutePath = $this->driver->getAbsolutePath($this->path, $path);
        if ($this->driver->isDirectory($absolutePath)) {
            return true;
        }
        return $this->driver->createDirectory($absolutePath, $this->permissions);
    }

    /**
     * Rename a file
     *
     * @param string $path
     * @param string $newPath
     * @param WriteInterface $targetDirectory
     * @return bool
     * @throws FilesystemException
     */
    public function renameFile($path, $newPath, WriteInterface $targetDirectory = null)
    {
        $this->assertIsFile($path);
        $targetDirectory = $targetDirectory ? : $this;
        if (!$targetDirectory->isExist($this->driver->getParentDirectory($newPath))) {
            $targetDirectory->create($this->driver->getParentDirectory($newPath));
        }
        $absolutePath = $this->driver->getAbsolutePath($this->path, $path);
        $absoluteNewPath = $targetDirectory->driver->getAbsolutePath($this->path, $newPath);
        $result = $this->driver->rename($absolutePath, $absoluteNewPath);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The "%s" path cannot be renamed into "%s"', $absolutePath, $absoluteNewPath)
            );
        }
        return $result;
    }

    /**
     * Copy a file
     *
     * @param string $path
     * @param string $destination
     * @param WriteInterface $targetDirectory
     * @return bool
     * @throws FilesystemException
     */
    public function copyFile($path, $destination, WriteInterface $targetDirectory = null)
    {
        $this->assertIsFile($path);

        $targetDirectory = $targetDirectory ? : $this;
        if (!$targetDirectory->isExist($this->driver->getParentDirectory($destination))) {
            $targetDirectory->create($this->driver->getParentDirectory($destination));
        }
        $absolutePath = $this->driver->getAbsolutePath($this->path, $path);
        $absoluteDestination = $targetDirectory->getAbsolutePath($destination);

        $result = $this->driver->copy($absolutePath, $absoluteDestination);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The "%s" path cannot be renamed into "%s"', $absolutePath, $absoluteDestination)
            );
        }
        return $result;
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
        if (!$this->isExist($path)) {
            return true;
        }
        $absolutePath = $this->driver->getAbsolutePath($this->path, $path);
        if ($this->driver->isFile($absolutePath)) {
            $this->driver->deleteFile($absolutePath);
        } else {
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
        $absolutePath = $this->driver->getAbsolutePath($this->path, $path);
        return $this->driver->changePermissions($absolutePath, $permissions);
    }

    /**
     * Sets modification time of file, if file does not exist - creates file
     *
     * @param string $path
     * @param int|null $modificationTime
     * @return bool
     * @throws FilesystemException
     */
    public function touch($path, $modificationTime = null)
    {
        $folder = $this->driver->getParentDirectory($path);
        $this->create($folder);
        $this->assertWritable($folder);
        return $this->driver->touch($this->driver->getAbsolutePath($this->path, $path), $modificationTime);
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
        return $this->driver->isWritable($this->driver->getAbsolutePath($this->path, $path));
    }

    /**
     * Open file in given mode
     *
     * @param string $path
     * @param string $mode
     * @param string|null $protocol
     * @return \Magento\Filesystem\File\WriteInterface
     */
    public function openFile($path, $mode = 'w', $protocol = null)
    {
        $folder = dirname($path);
        $this->create($folder);
        $this->assertWritable($folder);
        $absolutePath = $this->driver->getAbsolutePath($this->path, $path);
        return $this->fileFactory->create($absolutePath, $protocol, $this->driver, $mode);
    }

    /**
     * Write contents to file in given mode
     *
     * @param string $path
     * @param string $content
     * @param string|null $mode
     * @param string|null $protocol
     * @return int The number of bytes that were written.
     * @throws FilesystemException
     */
    public function writeFile($path, $content, $mode = 'w+', $protocol = null)
    {
        return $this->openFile($path, $mode, $protocol)->write($content);
    }
}
