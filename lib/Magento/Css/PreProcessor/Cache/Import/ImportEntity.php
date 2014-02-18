<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Import;

use Magento\Filesystem;

/**
 * Import entity
 */
class ImportEntity implements ImportEntityInterface
{
    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $rootDirectory;

    /**
     * @var string
     */
    protected $originalFile;

    /**
     * @var int
     */
    protected $originalMtime;

    /**
     * @param Filesystem $filesystem
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param string $filePath
     * @param array $params
     */
    public function __construct(
        Filesystem $filesystem,
        \Magento\View\FileSystem $viewFileSystem,
        $filePath,
        array $params
    ) {
        $this->initRootDir($filesystem);

        $absoluteFilePath = $viewFileSystem->getViewFile($filePath, $params);
        $relativePath = $this->rootDirectory->getRelativePath($absoluteFilePath);

        $this->originalFile = $relativePath;
        $this->originalMtime = $this->rootDirectory->stat($relativePath)['mtime'];
    }

    /**
     * @return string
     */
    public function getOriginalFile()
    {
        return $this->originalFile;
    }

    /**
     * @return int
     */
    public function getOriginalMtime()
    {
        return $this->originalMtime;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (!$this->isFileExist($this->getOriginalFile())) {
            return false;
        }
        $originalFileMTime = $this->rootDirectory->stat($this->getOriginalFile())['mtime'];
        return $originalFileMTime == $this->getOriginalMtime();
    }

    /**
     * @param string $filePath
     * @return bool
     */
    protected function isFileExist($filePath)
    {
        return $this->rootDirectory->isFile($filePath);
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['originalFile', 'originalMtime'];
    }

    /**
     * @return void
     */
    public function __wakeup()
    {
        $filesystem = \Magento\App\ObjectManager::getInstance()->get('Magento\Filesystem');
        $this->initRootDir($filesystem);
    }

    /**
     * @param Filesystem $filesystem
     * @return $this
     */
    protected function initRootDir(\Magento\Filesystem $filesystem)
    {
        $this->rootDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        return $this;
    }
}
