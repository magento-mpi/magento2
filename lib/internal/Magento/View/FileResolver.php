<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class FileResolver
{
    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    /**
     * @var \Magento\View\Asset\Service
     */
    protected $assetService;

    /**
     * @var \Magento\View\Publisher
     */
    protected $publisher;

    /**
     * @var \Magento\View\DeployedFilesManager
     */
    protected $deployedFileManager;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @param Service $viewService
     * @param Asset\Service $assetService
     * @param Publisher $publisher
     * @param DeployedFilesManager $deployedFileManager
     * @param \Magento\View\FileSystem $viewFileSystem
     */
    public function __construct(
        \Magento\View\Service $viewService,
        \Magento\View\Asset\Service $assetService,
        \Magento\View\Publisher $publisher,
        \Magento\View\DeployedFilesManager $deployedFileManager,
        \Magento\View\FileSystem $viewFileSystem
    ) {
        $this->viewService = $viewService;
        $this->assetService = $assetService;
        $this->publisher = $publisher;
        $this->deployedFileManager = $deployedFileManager;
        $this->viewFileSystem = $viewFileSystem;
    }

    /**
     * Get public file path
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getPublicViewFile($fileId, array $params = array())
    {
        $this->assetService->updateDesignParams($params);
        $filePath = $this->assetService->extractScope($this->viewFileSystem->normalizePath($fileId), $params);
        $publicFilePath = $this->getFilesManager()->getPublicViewFile($filePath, $params);

        return $publicFilePath;
    }

    /**
     * Get path to file
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFile($fileId, array $params = array())
    {
        $this->assetService->updateDesignParams($params);
        $filePath = $this->assetService->extractScope($this->viewFileSystem->normalizePath($fileId), $params);
        $viewFile = $this->getFilesManager()->getViewFile($filePath, $params);
        return $viewFile;
    }

    /**
     * Get files manager that is able to return file public path
     *
     * @return \Magento\View\FilesManagerInterface
     */
    protected function getFilesManager()
    {
        if ($this->viewService->isPublishingAllowed()) {
            $filesManager = $this->publisher;
        } else {
            $filesManager = $this->deployedFileManager;
        }

        return $filesManager;
    }
}
