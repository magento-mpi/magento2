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
        return $this->_adapter->exists($this->normalize($key));
    }

    /**
     * Reads content of the file.
     *
     * @param string $key
     * @return string
     */
    public function read($key)
    {
        return $this->_adapter->read($this->normalize($key));
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
        return $this->_adapter->write($this->normalize($key), $content);
    }

    /**
     * Deletes the file.
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->_adapter->delete($this->normalize($key));
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
        return $this->_adapter->rename($this->normalize($source), $this->normalize($target));
    }

    /**
     * Check if key is directory.
     *
     * @param string $key
     * @return bool
     */
    public function isDirectory($key)
    {
        return $this->_adapter->isDirectory($this->normalize($key));
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
     * Normalize path
     *
     * @param string $key
     * @return string
     */
    public function normalize($key)
    {
        $key = str_replace('\\', DIRECTORY_SEPARATOR, $key);
        $path = explode(DIRECTORY_SEPARATOR, $key);
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
        $path = DIRECTORY_SEPARATOR . ltrim(implode(DIRECTORY_SEPARATOR, $realPath), DIRECTORY_SEPARATOR);

        $this->_checkPath($path);
        return $path;
    }
}
