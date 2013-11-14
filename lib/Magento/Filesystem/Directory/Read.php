<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\Directory;

use Magento\Filesystem\FilesystemException;

class Read implements ReadInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var \Magento\Filesystem\File\ReadFactory
     */
    protected $fileFactory;

    /**
     * @param string $path
     * @param \Magento\Filesystem\File\ReadFactory $fileFactory
     */
    public function __construct($path, \Magento\Filesystem\File\ReadFactory $fileFactory)
    {
        $this->path = rtrim($path, '/') . '/';
        $this->fileFactory = $fileFactory;
    }

    /**
     * Returns last warning message string
     *
     * @return string
     */
    protected function _getWarningMessage()
    {
        $errorMessage = 'Warning!';
        $warning = error_get_last();
        if ($warning && $warning['type'] == E_WARNING) {
            $errorMessage .= $warning['message'];
        } else {
            $errorMessage .= 'Unexpected functions result';
        }
        return $errorMessage;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getAbsolutePath($path)
    {
        return $this->path . ltrim($path, '/');
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getRelativePath($path)
    {
        if (strpos($path, $this->path) === 0) {
            $result = substr($path, strlen($this->path));
        } else {
            $result = $path;
        }
        return $result;
    }

    /**
     * Validate of path existence
     *
     * @param string $path
     * @throws FilesystemException
     */
    protected function assertExist($path)
    {
        if ($this->isExist($path) === false) {
            throw new FilesystemException(sprintf('The path "%s" doesn\'t exist', $this->getAbsolutePath($path)));
        }
    }

    /**
     * Retrieve list of all entities in given path
     *
     * @param string|null $path
     * @return array
     */
    public function read($path = null)
    {
        $this->assertExist($path);

        $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS;
        $iterator = new \FilesystemIterator($this->getAbsolutePath($path), $flags);
        $result = array();
        /** @var \FilesystemIterator $file */
        foreach ($iterator as $file) {
            $result[] = $this->getRelativePath($file->getPathname());
        }
        return $result;
    }

    /**
     * Search all entries for given regex pattern
     *
     * @param string $pattern
     * @return array
     */
    public function search($pattern)
    {
        clearstatcache();
        $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS;
        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->path, $flags)
            ),
            $pattern
        );
        $result = array();
        /** @var \FilesystemIterator $file */
        foreach ($iterator as $file) {
            $result[] = $this->getRelativePath($file->getPathname());
        }
        return $result;
    }

    /**
     * Check a file or directory exists
     *
     * @param null $path
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function isExist($path = null)
    {
        clearstatcache();
        $result = @file_exists($this->getAbsolutePath($path));
        if ($result === null) {
            throw new FilesystemException($this->_getWarningMessage());
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
        $this->assertExist($path);
        $result = @stat($this->getAbsolutePath($path));
        if ($result === null) {
            throw new FilesystemException($this->_getWarningMessage());
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
        $result = @is_readable($this->getAbsolutePath($path));
        if ($result === null) {
            throw new FilesystemException($this->_getWarningMessage());
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
        $result = @is_file($this->getAbsolutePath($path));
        if ($result === null) {
            throw new FilesystemException($this->_getWarningMessage());
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
        $result = @is_dir($this->getAbsolutePath($path));
        if ($result === null) {
            throw new FilesystemException($this->_getWarningMessage());
        }
        return $result;
    }

    /**
     * Open file in read mode
     *
     * @param string $path
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function openFile($path)
    {
        return $this->fileFactory->create($this->getAbsolutePath($path));
    }

    /**
     * Retrieve file contents from given path
     *
     * @param string $path
     * @return string
     * @throws FilesystemException
     */
    public function readFile($path)
    {
        clearstatcache();
        $result = @file_get_contents($this->getAbsolutePath($path));
        if (!$result) {
            // @todo add message
            throw new FilesystemException($this->_getWarningMessage());
        }
        return $result;
    }
}
