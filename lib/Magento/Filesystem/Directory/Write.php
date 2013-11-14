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
     * @param string $path
     * @param \Magento\Filesystem\File\WriteFactory $fileFactory
     * @param $permissions
     */
    public function __construct($path, \Magento\Filesystem\File\WriteFactory $fileFactory, $permissions)
    {
        $this->path = rtrim($path, '/') . '/';
        $this->fileFactory = $fileFactory;
        $this->permissions = $permissions;
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
        if ($this->isDirectory($absolutePath)) {
            return true;
        }
        $result = @mkdir($absolutePath, $this->permissions, true);
        if (!$result) {
            throw new FilesystemException(sprintf('Directory "%s" cannot be created, Additional information (%s)',
                $absolutePath,
                $this->_getWarningMessage()
            ));
        }
        return $result;
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
        $result = @rename($absolutePath, $absoluteNewPath);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The "%s" path cannot be renamed into "%s". Additional information (%s)',
                    $absolutePath,
                    $absoluteNewPath,
                    $this->_getWarningMessage()
            ));
        }
        return $result;
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

        $result = @copy($absolutePath, $absoluteDestinationPath);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The file or directory "%s" cannot be copied to "%s". Additional information (%s)',
                    $absolutePath,
                    $absoluteDestinationPath,
                    $this->_getWarningMessage()
                ));
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
        if ($this->isFile($absolutePath)) {
            $result = @unlink($this->getAbsolutePath($path));
        } else {
            foreach ($this->read($path) as $subPath) {
                $this->delete($subPath);
            }
            $result = @rmdir($absolutePath);
        }
        if (!$result) {
            throw new FilesystemException(
                sprintf('The file or directory "%s" cannot be deleted. Additional information (%s)',
                    $absolutePath,
                    $this->_getWarningMessage()
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
        $this->assertExist($path);

        $absolutePath = $this->getAbsolutePath($path);
        $result = @chmod($absolutePath, $permissions);
        if (!$result) {
            throw new FilesystemException(
                sprintf('Cannot change permissions for path "%s". Additional information (%s)',
                    $absolutePath,
                    $this->_getWarningMessage()
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
        $absolutePath = $this->getAbsolutePath($path);

        $folder = dirname($path);
        $this->create($folder);
        $this->assertWritable($folder);

        if ($modificationTime === null) {
            $result = @touch($absolutePath);
        } else {
            $result = @touch($absolutePath, $modificationTime);
        }
        if (!$result) {
            throw new FilesystemException(
                sprintf('The file or directory "%s" cannot be touched. Additional information (%s)',
                    $absolutePath,
                    $this->_getWarningMessage()
                ));
        }
        return $result;
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
        clearstatcache();
        $result = @is_writable($this->getAbsolutePath($path));
        if ($result === null) {
            throw new FilesystemException($this->_getWarningMessage());
        }
        return $result;
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
        $result = @file_put_contents($absolutePath, $content, $mode);
        if (!$result) {
            throw new FilesystemException(
                sprintf('The specified "%s" file could not be written. Additional information (%s)',
                    $absolutePath,
                    $this->_getWarningMessage()
                ));
        }
        return $result;
    }
}
