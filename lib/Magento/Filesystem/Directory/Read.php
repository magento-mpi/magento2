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
     * Path to directory
     *
     * @var string
     */
    protected $path;

    /**
     * File factory
     *
     * @var \Magento\Filesystem\File\ReadFactory
     */
    protected $fileFactory;

    /**
     * Constructor. Set properties.
     *
     * @param array $config
     * @param \Magento\Filesystem\File\ReadFactory $fileFactory
     */
    public function __construct(array $config, \Magento\Filesystem\File\ReadFactory $fileFactory)
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
        if (isset($config['path'])) {
            $this->path = rtrim($config['path'], '/') . '/';
        } else {
            throw new FilesystemException('Cannot create directory without path');
        }
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
     * @param string|null $path
     * @return bool
     */
    public function isExist($path = null)
    {
        clearstatcache();

        return file_exists($this->getAbsolutePath($path));
    }

    /**
     * Gathers the statistics of the given path
     *
     * @param string $path
     * @return array
     */
    public function stat($path)
    {
        $this->assertExist($path);

        return stat($this->getAbsolutePath($path));
    }

    /**
     * Check permissions for reading file or directory
     *
     * @param string $path
     * @return bool
     */
    public function isReadable($path)
    {
        clearstatcache();

        return is_readable($this->getAbsolutePath($path));
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
        $absolutePath = $this->getAbsolutePath($path);
        clearstatcache();
        if (is_file($absolutePath) === false) {
            throw new FilesystemException(
                sprintf('The file "%s" either doesn\'t exist or not a file', $absolutePath)
            );
        }
        return file_get_contents($absolutePath);
    }
}