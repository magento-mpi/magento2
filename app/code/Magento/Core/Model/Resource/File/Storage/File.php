<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\File\Storage;

/**
 * Class File
 */
class File
{
    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Framework\Logger $log
     */
    public function __construct(\Magento\Framework\App\Filesystem $filesystem, \Magento\Framework\Logger $log)
    {
        $this->_logger = $log;
        $this->_filesystem = $filesystem;
    }

    /**
     * Collect files and directories recursively
     *
     * @param string $dir
     * @return array
     */
    public function getStorageData($dir = '/')
    {
        $files = array();
        $directories = array();
        $directoryInstance = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::MEDIA_DIR);
        if ($directoryInstance->isDirectory($dir)) {
            foreach ($directoryInstance->readRecursively($dir) as $path) {
                $itemName = basename($path);
                if ($itemName == '.svn' || $itemName == '.htaccess') {
                    continue;
                }
                if ($directoryInstance->isDirectory($path)) {
                    $directories[] = array(
                        'name' => $itemName,
                        'path' => dirname($path) == '.' ? '/' : dirname($path)
                    );
                } else {
                    $files[] = $path;
                }
            }
        }

        return array('files' => $files, 'directories' => $directories);
    }

    /**
     * Clear files and directories in storage
     *
     * @param string $dir
     * @return $this
     */
    public function clear($dir = '')
    {
        $directoryInstance = $this->_filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::MEDIA_DIR);
        if ($directoryInstance->isDirectory($dir)) {
            foreach ($directoryInstance->read($dir) as $path) {
                $directoryInstance->delete($path);
            }
        }

        return $this;
    }

    /**
     * Save directory to storage
     *
     * @param array $dir
     * @throws \Magento\Framework\Model\Exception
     * @return bool
     */
    public function saveDir($dir)
    {
        if (!isset($dir['name']) || !strlen($dir['name']) || !isset($dir['path'])) {
            return false;
        }

        $path = strlen($dir['path']) ? $dir['path'] . '/' . $dir['name'] : $dir['name'];

        try {
            $this->_filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::MEDIA_DIR)->create($path);
        } catch (\Exception $e) {
            $this->_logger->log($e->getMessage());
            throw new \Magento\Framework\Model\Exception(
                __('Unable to create directory: %1', \Magento\Framework\App\Filesystem::MEDIA_DIR . '/' . $path)
            );
        }

        return true;
    }

    /**
     * Save file to storage
     *
     * @param string $filePath
     * @param string $content
     * @param bool $overwrite
     * @throws \Magento\Framework\Model\Exception
     * @return bool
     */
    public function saveFile($filePath, $content, $overwrite = false)
    {
        try {
            $directoryInstance = $this->_filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::MEDIA_DIR);
            if (!$directoryInstance->isFile($filePath) || $overwrite && $directoryInstance->delete($filePath)) {
                $directoryInstance->writeFile($filePath, $content);
                return true;
            }
        } catch (\Magento\Framework\Filesystem\FilesystemException $e) {
            $this->_logger->log($e->getMessage());
            throw new \Magento\Framework\Model\Exception(__('Unable to save file: %1', $filePath));
        }

        return false;
    }
}
