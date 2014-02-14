<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model;

class Download
{
    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_rootDir;

    /**
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $_fileStorageDatabase;

    /**
     * @var \Magento\Core\Model\File\Storage\DatabaseFactory
     */
    protected $_storageDatabaseFactory;

    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase
     * @param \Magento\Core\Model\File\Storage\DatabaseFactory $storageDatabaseFactory
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\Core\Helper\File\Storage\Database $fileStorageDatabase,
        \Magento\Core\Model\File\Storage\DatabaseFactory $storageDatabaseFactory,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_rootDir = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->_fileStorageDatabase = $fileStorageDatabase;
        $this->_storageDatabaseFactory = $storageDatabaseFactory;
        $this->_fileFactory = $fileFactory;
    }

    /**
     * Custom options downloader
     *
     * @param mixed $info
     * @throws \Exception
     */
    public function downloadFile($info)
    {
        $relativePath = $info['order_path'];
        if ($this->_isCanProcessed($relativePath)) {
            //try get file from quote
            $relativePath = $info['quote_path'];
            if ($this->_isCanProcessed($relativePath)) {
                throw new \Exception();
            }
        }
        $this->_fileFactory->create(
            $info['title'],
            array('value' => $this->_rootDir->getAbsolutePath($relativePath), 'type' => 'filename'),
            \Magento\App\Filesystem::ROOT_DIR
        );
    }

    protected function _isCanProcessed($relativePath)
    {
        $filePath = $this->_rootDir->getAbsolutePath($relativePath);
        return (!$this->_rootDir->isFile($relativePath) || !$this->_rootDir->isReadable($relativePath))
        && !$this->_processDatabaseFile($filePath);
    }

    /**
     * Check file in database storage if needed and place it on file system
     *
     * @param string $filePath
     * @return bool
     */
    protected function _processDatabaseFile($filePath)
    {
        if (!$this->_fileStorageDatabase->checkDbUsage()) {
            return false;
        }
        $relativePath = $this->_fileStorageDatabase->getMediaRelativePath($filePath);
        $file = $this->_storageDatabaseFactory->create()->loadByFilename($relativePath);
        if (!$file->getId()) {
            return false;
        }
        $stream = $this->_rootDir->openFile($filePath, 'w+');
        $stream->lock();
        $stream->write($filePath, $file->getContent());
        $stream->unlock();
        $stream->close();
        return true;
    }
}
