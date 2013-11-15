<?php
/**
 * Magento filesystem facade
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

use Magento\Filesystem\FilesystemException;

class Filesystem
{
    /**
     * @var \Magento\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Filesystem\Directory\ReadFactory
     */
    protected $readFactory;

    /**
     * @var \Magento\Filesystem\Directory\WriteFactory
     */
    protected $writeFactory;

    /**
     * @var \Magento\Filesystem\Directory\Read[]
     */
    protected $readInstances = array();

    /**
     * @var \Magento\Filesystem\Directory\Write[]
     */
    protected $writeInstances = array();

    /**
     * @deprecated
     */
    const DIRECTORY_SEPARATOR = '/';

    /**
     * @var \Magento\Filesystem\AdapterInterface
     * @deprecated
     */
    protected $_adapter;

    /**
     * @var string
     * @deprecated
     */
    protected $_workingDirectory;

    /**
     * @var bool
     * @deprecated
     */
    protected $_isAllowCreateDirs = false;

    /**
     * @var int
     * @deprecated
     */
    protected $_newDirPermissions = 0777;

    /**
     * @param Filesystem\DirectoryList $directoryList
     * @param Filesystem\Directory\ReadFactory $readFactory
     * @param Filesystem\Directory\WriteFactory $writeFactory
     * @param Filesystem\AdapterInterface $adapter
     */
    public function __construct(
        \Magento\Filesystem\DirectoryList $directoryList,
        \Magento\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Filesystem\AdapterInterface $adapter
    ) {
        $this->directoryList = $directoryList;
        $this->readFactory = $readFactory;
        $this->writeFactory = $writeFactory;

        $this->_adapter = $adapter;
        $this->_workingDirectory = self::normalizePath(__DIR__ . '/../..');
    }

    /**
     * Create an instance of directory with write permissions
     *
     * @param string $code
     * @return \Magento\Filesystem\Directory\Read
     */
    public function getDirectoryRead($code)
    {
        if (!array_key_exists($code, $this->readInstances)) {
            $config = $this->directoryList->getConfig($code);
            $this->readInstances[$code] = $this->readFactory->create($config);
        }
        return $this->readInstances[$code];
    }

    /**
     * Create an instance of directory with read permissions
     *
     * @param string $code
     * @return \Magento\Filesystem\Directory\Write
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function getDirectoryWrite($code)
    {
        if (!array_key_exists($code, $this->writeInstances)) {
            $config = $this->directoryList->getConfig($code);
            if (isset($config['read_only']) && $config['read_only']) {
                throw new FilesystemException(sprintf('The "%s" directory doesn\'t allow write operations', $code));
            }
            $this->writeInstances[$code] = $this->writeFactory->create($config);
        }
        return $this->writeInstances[$code];
    }

    /**
     * Retrieve absolute path for for given code
     *
     * @param string $code
     * @return string
     */
    public function getPath($code)
    {
        $config = $this->directoryList->getConfig($code);
        return isset($config['path']) ? $config['path'] : '';
    }

    /**
     * Retrieve uri for given code
     *
     * @param string $code
     * @return string
     */
    public function getUri($code)
    {
        $config = $this->directoryList->getConfig($code);
        return isset($config['uri']) ? $config['uri'] : '';
    }

    /**
     * Sets working directory to restrict operations with filesystem.
     *
     * @param string $dir
     * @return \Magento\Filesystem
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function setWorkingDirectory($dir)
    {
        $dir = self::normalizePath($dir);
        if (!$this->_adapter->isDirectory($dir)) {
            throw new \InvalidArgumentException(sprintf('Working directory "%s" does not exists', $dir));
        }
        $this->_workingDirectory = $dir;
        return $this;
    }

    /**
     * Get current working directory.
     *
     * @return string
     * @deprecated
     */
    public function getWorkingDirectory()
    {
        return $this->_workingDirectory;
    }

    /**
     * Allows to create directories when process operations
     *
     * @param bool $allow
     * @param int $permissions
     * @return \Magento\Filesystem
     * @deprecated
     */
    public function setIsAllowCreateDirectories($allow, $permissions = null)
    {
        $this->_isAllowCreateDirs = (bool)$allow;
        if (null !== $permissions) {
            $this->_newDirPermissions = $permissions;
        }
        return $this;
    }

    /**
     * Checks the file existence.
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return bool
     * @deprecated
     */
    public function has($key, $workingDirectory = null)
    {
        return $this->_adapter->exists($this->_getCheckedPath($key, $workingDirectory));
    }

    /**
     * Reads content of the file.
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return string
     * @deprecated
     */
    public function read($key, $workingDirectory = null)
    {
        $path = $this->_getCheckedPath($key, $workingDirectory);
        $this->_checkFileExists($path);
        return $this->_adapter->read($path);
    }

    /**
     * Writes content into the file.
     *
     * @param string $key
     * @param string $content
     * @param string|null $workingDirectory
     * @return int The number of bytes that were written.
     * @deprecated
     */
    public function write($key, $content, $workingDirectory = null)
    {
        $path = $this->_getCheckedPath($key, $workingDirectory);
        $this->ensureDirectoryExists(dirname($path));
        return $this->_adapter->write($path, $content);
    }

    /**
     * Deletes the key.
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return bool
     * @deprecated
     */
    public function delete($key, $workingDirectory = null)
    {
        $path = $this->_getCheckedPath($key, $workingDirectory);
        return $this->_adapter->delete($path);
    }

    /**
     * Renames the file.
     *
     * @param string $source
     * @param string $target
     * @param string|null $workingDirectory
     * @param string|null $targetDirectory
     * @return bool
     * @deprecated
     */
    public function rename($source, $target, $workingDirectory = null, $targetDirectory = null)
    {
        if ($workingDirectory && null === $targetDirectory) {
            $targetDirectory = $workingDirectory;
        }
        $sourcePath = $this->_getCheckedPath($source, $workingDirectory);
        $targetPath = $this->_getCheckedPath($target, $targetDirectory);
        $this->_checkExists($sourcePath);
        $this->ensureDirectoryExists(dirname($targetPath), $this->_newDirPermissions, $targetDirectory);
        return $this->_adapter->rename($sourcePath, $targetPath);
    }

    /**
     * Copy the file.
     *
     * @param string $source
     * @param string $target
     * @param string|null $workingDirectory
     * @param string|null $targetDirectory
     * @return bool
     * @deprecated
     */
    public function copy($source, $target, $workingDirectory = null, $targetDirectory = null)
    {
        if ($workingDirectory && null === $targetDirectory) {
            $targetDirectory = $workingDirectory;
        }
        $sourcePath = $this->_getCheckedPath($source, $workingDirectory);
        $targetPath = $this->_getCheckedPath($target, $targetDirectory);
        $this->_checkFileExists($sourcePath);
        $this->ensureDirectoryExists(dirname($targetPath), $this->_newDirPermissions, $targetDirectory);
        return $this->_adapter->copy($sourcePath, $targetPath);
    }

    /**
     * Check if key is directory.
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return bool
     * @deprecated
     */
    public function isDirectory($key, $workingDirectory = null)
    {
        return $this->_adapter->isDirectory($this->_getCheckedPath($key, $workingDirectory));
    }

    /**
     * Check if key is file.
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return bool
     * @deprecated
     */
    public function isFile($key, $workingDirectory = null)
    {
        return $this->_adapter->isFile($this->_getCheckedPath($key, $workingDirectory));
    }

    /**
     * Check if key exists and is writable
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return bool
     * @deprecated
     */
    public function isWritable($key, $workingDirectory = null)
    {
        return $this->_adapter->isWritable($this->_getCheckedPath($key, $workingDirectory));
    }

    /**
     * Check if key exists and is readable
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return bool
     * @deprecated
     */
    public function isReadable($key, $workingDirectory = null)
    {
        return $this->_adapter->isReadable($this->_getCheckedPath($key, $workingDirectory));
    }

    /**
     * Change permissions of key
     *
     * @param string $key
     * @param int $permissions
     * @param bool $recursively
     * @param string|null $workingDirectory
     * @deprecated
     */
    public function changePermissions($key, $permissions, $recursively = false, $workingDirectory = null)
    {
        $this->_adapter->changePermissions($this->_getCheckedPath($key, $workingDirectory), $permissions, $recursively);
    }

    /**
     * Gets list of all nested keys
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return array
     * @deprecated
     */
    public function getNestedKeys($key, $workingDirectory = null)
    {
        return $this->_adapter->getNestedKeys($this->_getCheckedPath($key, $workingDirectory));
    }

    /**
     * Gets list of all matched keys
     *
     * @param string $baseDirectory
     * @param string $pattern
     * @return array
     * @deprecated
     */
    public function searchKeys($baseDirectory, $pattern)
    {
        $baseDirectory = $this->_getCheckedPath($baseDirectory);
        $this->_checkPathInWorkingDirectory(
            rtrim($baseDirectory, self::DIRECTORY_SEPARATOR)
            . self::DIRECTORY_SEPARATOR
            . ltrim($pattern, self::DIRECTORY_SEPARATOR)
        );
        return $this->_adapter->searchKeys(
            rtrim($baseDirectory, self::DIRECTORY_SEPARATOR)
            . self::DIRECTORY_SEPARATOR
            . ltrim(self::fixSeparator($pattern), self::DIRECTORY_SEPARATOR)
        );
    }

    /**
     * Creates new directory
     *
     * @param string $key
     * @param int $permissions
     * @param string|null $workingDirectory
     * @deprecated
     */
    public function createDirectory($key, $permissions = 0777, $workingDirectory = null)
    {
        $path = $this->_getCheckedPath($key, $workingDirectory);
        $parentPath = dirname($path);
        if (!$this->isDirectory($parentPath)) {
            $this->createDirectory($parentPath, $permissions, $workingDirectory);
        }
        $this->_adapter->createDirectory($path, $permissions);
    }

    /**
     * Create directory if it does not exists.
     *
     * @param string $key
     * @param int $permissions
     * @param string|null $workingDirectory
     * @throws \Magento\Filesystem\FilesystemException
     * @deprecated
     */
    public function ensureDirectoryExists($key, $permissions = 0777, $workingDirectory = null)
    {
        if (!$this->isDirectory($key, $workingDirectory)) {
            if ($this->_isAllowCreateDirs) {
                $this->createDirectory($key, $permissions, $workingDirectory);
            } else {
                throw new \Magento\Filesystem\FilesystemException("Directory '$key' doesn't exist.");
            }
        }
    }

    /**
     * Sets access and modification time of file.
     *
     * @param string $key
     * @param int|null $fileModificationTime
     * @param string|null $workingDirectory
     * @deprecated
     */
    public function touch($key, $fileModificationTime = null, $workingDirectory = null)
    {
        $key = $this->_getCheckedPath($key, $workingDirectory);
        $this->ensureDirectoryExists(dirname($key), $this->_newDirPermissions);
        $this->_adapter->touch($key, $fileModificationTime);
    }

    /**
     * Get file modification time.
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return int
     * @deprecated
     */
    public function getMTime($key, $workingDirectory = null)
    {
        $key = $this->_getCheckedPath($key, $workingDirectory);
        $this->_checkExists($key);
        return $this->_adapter->getMTime($key);
    }

    /**
     * Get file size.
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return int
     * @deprecated
     */
    public function getFileSize($key, $workingDirectory = null)
    {
        $key = $this->_getCheckedPath($key, $workingDirectory);
        $this->_checkFileExists($key);
        return $this->_adapter->getFileSize($key);
    }

    /**
     * Creates stream object
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return \Magento\Filesystem\StreamInterface
     * @throws \Magento\Filesystem\FilesystemException
     * @deprecated
     */
    public function createStream($key, $workingDirectory = null)
    {
        $key = $this->_getCheckedPath($key, $workingDirectory);
        if ($this->_adapter instanceof \Magento\Filesystem\Stream\FactoryInterface) {
            return $this->_adapter->createStream($key);
        } else {
            throw new \Magento\Filesystem\FilesystemException("Filesystem doesn't support streams.");
        }
    }

    /**
     * Creates stream object and opens it
     *
     * @param string $key
     * @param \Magento\Filesystem\Stream\Mode|string $mode
     * @param string|null $workingDirectory
     * @return \Magento\Filesystem\StreamInterface
     * @throws \InvalidArgumentException
     * @deprecated
     */
    public function createAndOpenStream($key, $mode, $workingDirectory = null)
    {
        $stream = $this->createStream($key, $workingDirectory);
        if (!$mode instanceof \Magento\Filesystem\Stream\Mode && !is_string($mode)) {
            throw new \InvalidArgumentException('Wrong mode parameter');
        }
        $stream->open($mode);
        return $stream;
    }

    /**
     * Calculates the md5 hash of a given file
     *
     * @param string $key
     * @param string $workingDirectory
     * @return string
     * @deprecated
     */
    public function getFileMd5($key, $workingDirectory = null)
    {
        $key = $this->_getCheckedPath($key, $workingDirectory);
        $this->_checkFileExists($key);
        return $this->_adapter->getFileMd5($key);
    }

    /**
     * Check that file exists
     *
     * @param string $path
     * @throws \InvalidArgumentException
     * @deprecated
     */
    protected function _checkFileExists($path)
    {
        if (!$this->_adapter->isFile($path)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not exists', $path));
        }
    }

    /**
     * Check that file or directory exists
     *
     * @param string $path
     * @throws \InvalidArgumentException
     * @deprecated
     */
    protected function _checkExists($path)
    {
        if (!$this->_adapter->exists($path)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not exists', $path));
        }
    }

    /**
     * Get absolute checked path
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return string
     * @deprecated
     */
    protected function _getCheckedPath($key, $workingDirectory = null)
    {
        $this->_checkPathInWorkingDirectory($key, $workingDirectory);
        return self::normalizePath($key);
    }

    /**
     * Asserts path in working directory
     *
     * @param string $key
     * @param string|null $workingDirectory
     * @return string
     * @throws \InvalidArgumentException
     * @deprecated
     */
    protected function _checkPathInWorkingDirectory($key, $workingDirectory = null)
    {
        $workingDirectory = $workingDirectory ? $workingDirectory : $this->_workingDirectory;
        if (!$this->isPathInDirectory($key, $workingDirectory)) {
            throw new \InvalidArgumentException("Path '$key' is out of working directory '$workingDirectory'");
        }
    }

    /**
     * Normalize the specified path by removing excessive '.', '..' and fixing directory separator
     *
     * @param string $path
     * @param bool $isRelative Flag that identify, that filename is relative, so '..' at the beginning is supported
     * @return string
     * @throws \Magento\Filesystem\FilesystemException if file can't be normalized
     */
    public static function normalizePath($path, $isRelative = false)
    {
        $fixedPath = self::fixSeparator($path);
        $parts = explode(self::DIRECTORY_SEPARATOR, $fixedPath);
        $result = array();

        foreach ($parts as $part) {
            if ('..' === $part) {
                if ($isRelative) {
                    if (!count($result) || ($result[count($result) - 1] == '..')) {
                        $result[] = $part;
                    } else {
                        array_pop($result);
                    }
                } else if (!array_pop($result)) {
                    throw new \Magento\Filesystem\FilesystemException("Invalid path '{$path}'.");
                }
            } else if ('.' !== $part) {
                $result[] = $part;
            }
        }
        return implode(self::DIRECTORY_SEPARATOR, $result);
    }

    /**
     * Update directory separator
     *
     * @static
     * @param string $path
     * @return string
     * @deprecated
     */
    public static function fixSeparator($path)
    {
        return rtrim(str_replace('\\', self::DIRECTORY_SEPARATOR, $path), self::DIRECTORY_SEPARATOR);
    }

    /**
     * Checks is directory contains path
     *
     * @param string $path
     * @param string $directory
     * @return bool
     */
    public function isPathInDirectory($path, $directory)
    {
        return 0 === strpos(self::normalizePath($path), self::normalizePath($directory));
    }

    /**
     * Check LFI protection
     *
     * @throws \InvalidArgumentException
     * @param string $name
     * @return bool
     * @deprecated
     */
    public function checkLfiProtection($name)
    {
        if (preg_match('#\.\.[\\\/]#', $name)) {
            throw new \InvalidArgumentException(
                'Requested file may not include parent directory traversal ("../", "..\\" notation)'
            );
        }
        return true;
    }
}
