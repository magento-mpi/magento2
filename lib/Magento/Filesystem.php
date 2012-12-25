<?php
/**
 * Magento filesystem facade
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem implements Magento_Filesystem_AdapterInterface
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
     * @param Magento_Filesystem_AdapterInterface $adapter
     */
    public function __construct(Magento_Filesystem_AdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
        $this->setWorkingDirectory(self::getPathFromArray(array(__DIR__, '..', '..')));
    }

    /**
     * Sets working directory to restrict operations with filesystem.
     *
     * @param string $dir
     */
    public function setWorkingDirectory($dir)
    {
        $this->_workingDirectory = $dir;
    }

    /**
     * Checks the file existence.
     *
     * @param string $key
     * @return bool
     */
    public function exists($key)
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
