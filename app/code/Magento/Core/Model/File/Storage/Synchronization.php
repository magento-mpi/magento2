<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\File\Storage;

class Synchronization
{
    /**
     * Database storage factory
     *
     * @var Magento_Core_Model_File_Storage_DatabaseFactory
     */
    protected $_storageFactory;

    /**
     * File stream handler
     *
     * @var \Magento\Io\File
     */
    protected $_streamFactory;

    /**
     * @param Magento_Core_Model_File_Storage_DatabaseFactory $storageFactory
     * @param Magento_Filesystem_Stream_LocalFactory $streamFactory
     */
    public function __construct(
        Magento_Core_Model_File_Storage_DatabaseFactory $storageFactory,
        Magento_Filesystem_Stream_LocalFactory $streamFactory
    ) {
        $this->_storageFactory = $storageFactory;
        $this->_streamFactory = $streamFactory;
    }

    /**
     * Synchronize file
     *
     * @param string $relativeFileName
     * @param string $filePath
     * @throws \LogicException
     */
    public function synchronize($relativeFileName, $filePath)
    {
        /** @var $storage \Magento\Core\Model\File\Storage\Database */
        $storage = $this->_storageFactory->create();
        try {
            $storage->loadByFilename($relativeFileName);
        } catch (\Exception $e) {
        }
        if ($storage->getId()) {
            $directory = dirname($filePath);
            if (!is_dir($directory) && !mkdir($directory, 0777, true)) {
                throw new \LogicException('Could not create directory');
            }

            /** @var \Magento\Filesystem\StreamInterface $stream */
            $stream = $this->_streamFactory->create(array('path' => $filePath));
            try{
                $stream->open('w');
                $stream->lock(true);
                $stream->write($storage->getContent());
                $stream->unlock();
                $stream->close();
            } catch (\Magento\Filesystem\FilesystemException $e) {
                $stream->close();
            }
        }
    }
}
