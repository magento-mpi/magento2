<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_File_Storage_Config
{
    /**
     * Config cache file path
     *
     * @var string
     */
    protected $_cacheFile;

    /**
     * Loaded config
     *
     * @var array
     */
    protected $_config;

    /**
     * File stream handler
     *
     * @var Magento_Io_File
     */
    protected $_streamFactory;

    /**
     * @param Magento_Core_Model_File_Storage $storage
     * @param Magento_Filesystem_Stream_LocalFactory $streamFactory
     * @param string $cacheFile
     */
    public function __construct(
        Magento_Core_Model_File_Storage $storage, Magento_Filesystem_Stream_LocalFactory $streamFactory, $cacheFile
    ) {
        $this->_config = $storage->getScriptConfig();
        $this->_streamFactory = $streamFactory;
        $this->_cacheFile = $cacheFile;
    }

    /**
     * Retrieve media directory
     *
     * @return string
     */
    public function getMediaDirectory()
    {
        return $this->_config['media_directory'];
    }

    /**
     * Retrieve list of allowed resources
     *
     * @return array
     */
    public function getAllowedResources()
    {
        return $this->_config['allowed_resources'];
    }

    /**
     * Save config in cache file
     */
    public function save()
    {
        /** @var Magento_Filesystem_StreamInterface $stream */
        $stream = $this->_streamFactory->create(array('path' => $this->_cacheFile));
        try{
            $stream->open('w');
            $stream->lock(true);
            $stream->write(json_encode($this->_config));
            $stream->unlock();
            $stream->close();
        } catch (Magento_Filesystem_Exception $e) {
            $stream->close();
        }
    }
}
