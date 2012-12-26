<?php
/**
 * Magento filesystem file
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_File
{
    /**
     * File path
     *
     * @var string
     */
    protected $_key;

    /**
     * Filesystem object
     *
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Content variable is lazy. It will not be read from filesystem until it's requested first time
     *
     * @var string
     */
    protected $_content = null;

    /**
     * Constructor
     *
     * @param string     $key
     * @param Magento_Filesystem $filesystem
     */
    public function __construct($key, Magento_Filesystem $filesystem)
    {
        $this->_key = $key;
        $this->_name = $key;
        $this->_filesystem = $filesystem;
    }

    /**
     * Returns the key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Returns the content
     *
     * @return string
     */
    public function getContent()
    {
        if (isset($this->_content)) {
            return $this->_content;
        }
        return $this->_content = $this->_filesystem->read($this->_key);
    }

    /**
     * Sets the content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * Indicates whether the file exists in the filesystem
     *
     * @return bool
     */
    public function exists()
    {
        return $this->_filesystem->has($this->_key);
    }

    /**
     * Deletes the file from the filesystem
     *
     * @return bool
     */
    public function delete()
    {
        return $this->_filesystem->delete($this->_key);
    }

    /**
     * Creates a new file stream instance of the file
     *
     * @return Magento_Filesystem
     */
    public function createStream()
    {
        return $this->_filesystem->createStream($this->_key);
    }
}
