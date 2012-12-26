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
    protected $_isAllowCreateDirectories = false;

    /**
     * @var int
     */
    protected $_newDirectoryMode = 0777;

    /**
     * @param Magento_Filesystem_AdapterInterface $adapter
     */
    public function __construct(Magento_Filesystem_AdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
        $this->setWorkingDirectory(self::getAbsolutePath(self::getPathFromArray(array(__DIR__, '..', '..'))));
    }

    /**
     * Sets working directory to restrict operations with filesystem.
     *
     * @param string $dir
     * @return Magento_Filesystem
     */
    public function setWorkingDirectory($dir)
    {
        $this->_workingDirectory = $dir;
        return $this;
    }

    /**
     * Allows to create directories when process operations
     *
     * @param bool $allow
     * @param int $mode
     * @return Magento_Filesystem
     */
    public function setIsAllowCreateDirectories($allow, $mode = null)
    {
        $this->_isAllowCreateDirectories = (bool)$allow;
        if (null !== $mode) {
            $this->_newDirectoryMode = $mode;
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
        return $this->_adapter->read($this->_getCheckedPath($key));
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
        return $this->_adapter->write($this->_getCheckedPath($key), $content);
    }

    /**
     * Deletes the file.
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->_adapter->delete($this->_getCheckedPath($key));
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
        return $this->_adapter->rename($this->_getCheckedPath($source), $this->_getCheckedPath($target));
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
     * Creates new directory
     *
     * @param string $key
     * @param int $mode
     */
    public function createDirectory($key, $mode = 0777)
    {
        $this->_adapter->createDirectory($key, $mode);
    }

    public function ensureDirectoryExists($key, $mode = 0777)
    {
        if (!$this->isDirectory($key)) {
            $this->createDirectory($key, $mode);
        }
    }

    public function touch($key)
    {
        if (!$this->isDirectory(dirname($key)) && $this->_isAllowCreateDirectories) {
            $this->createDirectory(dirname($key), $this->_newDirectoryMode);
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
        if ($this->_adapter instanceof Magento_Filesystem_Stream_FactoryInterface) {
            $result = $this->_adapter->createStream($key);
        } else {
            $result = new Magento_Filesystem_Stream_Memory($this, $key);
        }
        if (is_string($mode)) {
            $mode = new Magento_Filesystem_Stream_Mode($mode);
        } elseif (!$mode instanceof Magento_Filesystem_Stream_Mode) {
            throw new InvalidArgumentException('Wrong mode parameter');
        }
        $result->open($mode);
        return $result;
    }

    /**
     * Creates file object
     *
     * @param string $key
     * @return Magento_Filesystem_File
     */
    public function createFile($key)
    {
        return new Magento_Filesystem_File($key, $this);
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
        $path = implode(DIRECTORY_SEPARATOR, $path);
        if ($isAbsolute && !$isWindows) {
            return DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
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
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        return explode(DIRECTORY_SEPARATOR, ltrim($path, DIRECTORY_SEPARATOR));
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
