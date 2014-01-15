<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Core\Model\File\Storage;

use Magento\Filesystem\Directory\WriteInterface as DirectoryWrite,
    Magento\Filesystem\File\Write,
    Magento\Filesystem\FilesystemException;

/**
 * Class Synchronization
 */
class Synchronization
{
    /**
     * Database storage factory
     *
     * @var \Magento\Core\Model\File\Storage\DatabaseFactory
     */
    protected $storageFactory;

    /**
     * File stream handler
     *
     * @var DirectoryWrite
     */
    protected $pubDirectory;

    /**
     * @param \Magento\Core\Model\File\Storage\DatabaseFactory $storageFactory
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Model\File\Storage\DatabaseFactory $storageFactory,
        \Magento\Filesystem $filesystem
    ) {
        $this->storageFactory = $storageFactory;
        $this->pubDirectory = $filesystem->getDirectoryWrite(\Magento\Filesystem::PUB);
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
        $storage = $this->storageFactory->create();
        try {
            $storage->loadByFilename($relativeFileName);
        } catch (\Exception $e) {
        }
        if ($storage->getId()) {
            /** @var Write $file */
            $file = $this->pubDirectory->openFile($this->pubDirectory->getRelativePath($filePath), 'w');
            try{
                $file->lock();
                $file->write($storage->getContent());
                $file->unlock();
                $file->close();
            } catch (FilesystemException $e) {
                $file->close();
            }
        }
    }
}
