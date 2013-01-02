<?php
/**
 * Magento filesystem facade
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem
{
    const DIRECTORY_SEPARATOR = '/';

    /**
     * @var Magento_Filesystem_AdapterInterface
     */
    protected $_adapter;

    /**
     * @var string
     */
    protected $_workingDirectory;

    /**
     * @var bool
     */
    protected $_isAllowCreateDirs = false;

    /**
     * @var int
     */
    protected $_newDirPermissions = 0777;

    /**
     * Initialize adapter and default working directory.
     *
     * @param Magento_Filesystem_AdapterInterface $adapter
     */
    public function __construct(Magento_Filesystem_AdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
        $this->_workingDirectory = self::getAbsolutePath(self::getPathFromArray(array(__DIR__, '..', '..')));
    }

    /**
     * Sets working directory to restrict operations with filesystem.
     *
     * @param string $dir
     * @return Magento_Filesystem
     * @throws InvalidArgumentException
     */
    public function setWorkingDirectory($dir)
    {
        if (!$this->_adapter->isDirectory($dir)) {
            throw new InvalidArgumentException(sprintf('Working directory "%s" does not exists', $dir));
        }
        $this->_workingDirectory = $dir;
        return $this;
    }

    /**
     * Allows to create directories when process operations
     *
     * @param bool $allow
     * @param int $permissions
     * @return Magento_Filesystem
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
     * @return bool
     */
    public function has($key)
    {
        return $this->_adapter->exists($this->_getCheckedPath($key));
    }

    /**
     * Reads content of the file.
     *
     * @param string $key
     * @return string
     */
    public function read($key)
    {
        $path = $this->_getCheckedPath($key);
        $this->_checkFileExists($path);
        return $this->_adapter->read($path);
    }

    /**
     * Writes content into the file.
     *
     * @param string $key
     * @param string $content
     * @return int The number of bytes that were written.
     */
    public function write($key, $content)
    {
        $path = $this->_getCheckedPath($key);
        $this->ensureDirectoryExists(dirname($path));
        return $this->_adapter->write($path, $content);
    }

    /**
     * Deletes the key.
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $path = $this->_getCheckedPath($key);
        $this->_checkFileExists($path);
        return $this->_adapter->delete($path);
    }

    /**
     * Renames the file.
     *
     * @param string $source
     * @param string $target
     * @return bool
     */
    public function rename($source, $target)
    {
        $sourcePath = $this->_getCheckedPath($source);
        $this->_checkFileExists($sourcePath);
        return $this->_adapter->rename($sourcePath, $this->_getCheckedPath($target));
    }

    /**
     * Copy the file.
     *
     * @param string $source
     * @param string $target
     * @return bool
     */
    public function copy($source, $target)
    {
        $sourcePath = $this->_getCheckedPath($source);
        $this->_checkFileExists($sourcePath);
        return $this->_adapter->copy($sourcePath, $this->_getCheckedPath($target));
    }


    /**
     * Check if key is directory.
     *
     * @param string $key
     * @return bool
     */
    public function isDirectory($key)
    {
        return $this->_adapter->isDirectory($this->_getCheckedPath($key));
    }

    /**
     * Check if key is file.
     *
     * @param string $key
     * @return bool
     */
    public function isFile($key)
    {
        return $this->_adapter->isFile($this->_getCheckedPath($key));
    }

    /**
     * Check if key exists and is writable
     *
     * @param string $key
     * @return bool
     */
    public function isWritable($key)
    {
        return $this->_adapter->isWritable($this->_getCheckedPath($key));
    }

    /**
     * Check if key exists and is readable
     *
     * @param string $key
     * @return bool
     */
    public function isReadable($key)
    {
        return $this->_adapter->isReadable($this->_getCheckedPath($key));
    }

    /**
     * Change permissions of key
     *
     * @param string $key
     * @param int $permissions
     * @param bool $recursively
     */
    public function changePermissions($key, $permissions, $recursively = false)
    {
        $this->_adapter->changePermissions($this->_getCheckedPath($key), $permissions, $recursively);
    }

    /**
     * Gets list of all nested keys
     *
     * @param string $key
     * @return array
     */
    public function getNestedKeys($key)
    {
        return $this->_adapter->getNestedKeys($this->_getCheckedPath($key));
    }

    /**
     * Creates new directory
     *
     * @param string $key
     * @param int $permissions
     */
    public function createDirectory($key, $permissions = 0777)
    {
        $this->_adapter->createDirectory($this->_getCheckedPath($key), $permissions);
    }

    /**
     * Create directory if it does not exists.
     *
     * @param string $key
     * @param int $permissions
     */
    public function ensureDirectoryExists($key, $permissions = 0777)
    {
        if (!$this->isDirectory($key)) {
            $this->createDirectory($key, $permissions);
        }
    }

    /**
     * Sets access and modification time of file.
     *
     * @param string $key
     */
    public function touch($key)
    {
        $key = $this->_getCheckedPath($key);
        if ($this->_isAllowCreateDirs) {
            $this->ensureDirectoryExists(dirname($key), $this->_newDirPermissions);
        }
        $this->_adapter->touch($key);
    }

    /**
     * Creates stream object
     *
     * @param string $key
     * @return Magento_Filesystem_StreamInterface
     */
    public function createStream($key)
    {
        $key = $this->_getCheckedPath($key);
        if ($this->_adapter instanceof Magento_Filesystem_Stream_FactoryInterface) {
            return $this->_adapter->createStream($key);
        }
        return new Magento_Filesystem_Stream_Memory($this, $key);
    }

    /**
     * Creates stream object and opens it
     *
     * @param string $key
     * @param Magento_Filesystem_Stream_Mode|string $mode
     * @return Magento_Filesystem_StreamInterface
     * @throws InvalidArgumentException
     */
    public function createAndOpenStream($key, $mode)
    {
        $stream = $this->createStream($key);
        if (is_string($mode)) {
            $mode = new Magento_Filesystem_Stream_Mode($mode);
        } elseif (!$mode instanceof Magento_Filesystem_Stream_Mode) {
            throw new InvalidArgumentException('Wrong mode parameter');
        }
        $stream->open($mode);
        return $stream;
    }

    /**
     * Check that file exists
     *
     * @param string $path
     * @throws InvalidArgumentException
     */
    protected function _checkFileExists($path)
    {
        if (!$this->_adapter->exists($path)) {
            throw new InvalidArgumentException(sprintf('"%s" does not exists', $path));
        }
    }

    /**
     * Get absolute checked path
     *
     * @param string $key
     * @return string
     */
    protected function _getCheckedPath($key)
    {
        $path = self::getAbsolutePath($key);
        $this->_checkPath($path);
        return $path;
    }

    /**
     * Check path isolation
     *
     * @param string $key
     * @throws InvalidArgumentException
     */
    protected function _checkPath($key)
    {
        if ($this->_workingDirectory) {
            if (0 !== strpos($key, $this->_workingDirectory)) {
                throw new InvalidArgumentException('Invalid path');
            }
        }
    }

    /**
     * Get absolute path.
     *
     * @param string $key
     * @return string
     */
    public static function getAbsolutePath($key)
    {
        $path = self::getPathAsArray($key);
        $realPath = array();
        foreach ($path as $dir) {
            if ('.' == $dir) {
                continue;
            }
            if ('..' == $dir) {
                if (count($realPath) > 0) {
                    array_pop($realPath);
                }
                continue;
            }
            $realPath[] = $dir;
        }
        return self::getPathFromArray($realPath);
    }

    /**
     * Get path as string from array.
     *
     * @param array $path
     * @param bool $isAbsolute
     * @return string
     * @throws InvalidArgumentException
     */
    public static function getPathFromArray(array $path, $isAbsolute = true)
    {
        if (!count($path)) {
            throw new InvalidArgumentException('Path must contain at least one node');
        }
        $isWindows = preg_match('/\w:/', $path[0]);
        $path = implode(self::DIRECTORY_SEPARATOR, $path);
        if ($isAbsolute && !$isWindows) {
            return self::DIRECTORY_SEPARATOR . ltrim($path, self::DIRECTORY_SEPARATOR);
        } else {
            return $path;
        }
    }

    /**
     * Get path as array
     *
     * @param string $path
     * @return array
     */
    public static function getPathAsArray($path)
    {
        $path = str_replace('\\', self::DIRECTORY_SEPARATOR, $path);
        return explode(self::DIRECTORY_SEPARATOR, ltrim($path, self::DIRECTORY_SEPARATOR));
    }

    /**
     * Check is path absolute
     *
     * @param string $path
     * @return bool
     */
    public static function isAbsolutePath($path)
    {
        return $path == self::getAbsolutePath($path);
    }
}
