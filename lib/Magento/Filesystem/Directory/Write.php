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
     * Permissions for created staff
     *
     * @var int
     */
    protected $permissions = 0777;

    /**
     * Is allowed to create directories
     *
     * @var bool
     */
    protected $allowCreateDirs = true;

    /**
     * @param array $config
     * @param \Magento\Filesystem\File\WriteFactory $fileFactory
     */
    public function __construct(array $config, \Magento\Filesystem\File\WriteFactory $fileFactory)
    {
        $this->setProperties($config);
        $this->fileFactory = $fileFactory;
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
     * Check if given path is exists and is file
     *
     * @param string $path
     * @throws \Magento\Filesystem\FilesystemException
     */
    protected function assertIsFile($path)
    {
        clearstatcache();
        $absolutePath = $this->getAbsolutePath($path);
        if (!is_file($absolutePath)) {
            throw new FilesystemException(sprintf('The "%s" file doesn\'t exist or not a file', $absolutePath));
        }
    }

    /**
     * Check it directory is writable
     *
     * @throws \Magento\Filesystem\FilesystemException
     */
    protected function assertWritable($path)
    {
        clearstatcache();
        $absolutePath = $this->getAbsolutePath($path);
        if (is_writable($absolutePath) === false) {
            throw new FilesystemException(sprintf('The path "%s" is not writable', $absolutePath));
        }
    }

    /**
     * Recursively asserts parent folder are either not exists or exists and have write permissions
     *
     * @param string $absolutePath
     * @throws \Magento\Filesystem\FilesystemException
     */
    protected function assertParentsWritable($absolutePath)
    {
        clearstatcache();
        if (!is_writable($absolutePath)) {
            if (file_exists($absolutePath)) {
                throw new FilesystemException(sprintf('The path "%s" is not writable', $absolutePath));
            } else {
                $this->assertParentsWritable(dirname($absolutePath));
            }
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
        if (!$this->allowCreateDirs) {
            throw new FilesystemException('Operation is not allowed for the specified path: "%s"', $this->path);
        }
        clearstatcache();
        $absolutePath = $this->getAbsolutePath($path);
        if (is_dir($absolutePath)) {
            return true;
        } elseif (is_file($absolutePath)) {
            throw new FilesystemException(sprintf('The "%s" file already exists', $absolutePath));
        }
        $this->assertParentsWritable($absolutePath);

        $result = mkdir($absolutePath, $this->permissions, true);
        if ($result === false) {
            throw new FilesystemException(sprintf('Directory "%s" cannot be created', $absolutePath));
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
        $this->assertExist($path);

        $absolutePath = $this->getAbsolutePath($path);
        if (is_file($absolutePath)) {
            $result = unlink($this->getAbsolutePath($path));
        } else {
            foreach ($this->read($path) as $subPath) {
                $this->delete($subPath);
            }
            $result = rmdir($absolutePath);
        }
        if ($result === false) {
            throw new FilesystemException(sprintf('The file or directory "%s" cannot be deleted', $absolutePath));
        }
        return $result;
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
        if (!$targetDirectory->isExist(dirname($newPath))) {
            $targetDirectory->create(dirname($newPath));
        }

        $absolutePath = $this->getAbsolutePath($path);
        $absoluteNewPath = $targetDirectory->getAbsolutePath($newPath);

        $result = rename($absolutePath, $absoluteNewPath);
        if ($result === null) {
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
        if (!$targetDirectory->isExist(dirname($destination))) {
            $targetDirectory->create(dirname($destination));
        }

        $absolutePath = $this->getAbsolutePath($path);
        $absoluteDestination = $targetDirectory->getAbsolutePath($destination);

        $result = copy($absolutePath, $absoluteDestination);
        if ($result === null) {
            throw new FilesystemException(
                sprintf('The "%s" path cannot be renamed into "%s"', $absolutePath, $absoluteDestination)
            );
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
        $this->assertExist($path);

        $absolutePath = $this->getAbsolutePath($path);
        $result = chmod($absolutePath, $permissions);
        if ($result === false) {
            throw new FilesystemException(sprintf('Cannot change permissions for "%s" path', $absolutePath));
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
        $absolutePath = $this->getAbsolutePath($path);

        $folder = dirname($path);
        $this->create($folder);
        $this->assertWritable($folder);

        if ($modificationTime === null) {
            $result = touch($absolutePath);
        } else {
            $result = touch($absolutePath, $modificationTime);
        }
        if ($result === false) {
            throw new FilesystemException(sprintf('The file or directory "%s" cannot be touched', $absolutePath));
        }
        return $result;
    }

    /**
     * Check if given path is writable
     *
     * @param string|null $path
     * @return bool
     */
    public function isWritable($path = null)
    {
        clearstatcache();

        return is_writable($this->getAbsolutePath($path));
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

        $folder = dirname($path);
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

        $folder = dirname($path);
        $this->create($folder);
        $this->assertWritable($folder);

        $result = file_put_contents($absolutePath, $content, $mode);
        if ($result === null) {
            throw new FilesystemException(sprintf('The specified "%s" file could not be written', $absolutePath));
        }
        return $result;
    }
}