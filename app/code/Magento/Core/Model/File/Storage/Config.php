<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\File\Storage;

use Magento\Filesystem\DirectoryList,
    Magento\Filesystem\Directory\Write as DirectoryWrite,
    Magento\Filesystem\File\Write,
    Magento\Filesystem\FilesystemException;

class Config
{
    /**
     * Config cache file path
     *
     * @var string
     */
    protected $cacheFilePath;

    /**
     * Loaded config
     *
     * @var array
     */
    protected $config;

    /**
     * File stream handler
     *
     * @var DirectoryWrite
     */
    protected $pubDirectory;

    /**
     * @param \Magento\Core\Model\File\Storage $storage
     * @param \Magento\Filesystem $filesystem
     * @param string $cacheFile
     */
    public function __construct(
        \Magento\Core\Model\File\Storage $storage,
        \Magento\Filesystem $filesystem,
        $cacheFile
    ) {
        $this->config = $storage->getScriptConfig();
        $this->pubDirectory = $filesystem->getDirectoryWrite(DirectoryList::PUB);
        $this->cacheFilePath = $cacheFile;
    }

    /**
     * Retrieve media directory
     *
     * @return string
     */
    public function getMediaDirectory()
    {
        return $this->config['media_directory'];
    }

    /**
     * Retrieve list of allowed resources
     *
     * @return array
     */
    public function getAllowedResources()
    {
        return $this->config['allowed_resources'];
    }

    /**
     * Save config in cache file
     */
    public function save()
    {
        /** @var Write $file */
        $file = $this->pubDirectory->openFile($this->pubDirectory->getRelativePath($this->cacheFilePath), 'w');
        try{
            $file->lock(true);
            $file->write(json_encode($this->config));
            $file->unlock();
            $file->close();
        } catch (FilesystemException $e) {
            $file->close();
        }
    }
}
