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
     * @var \Magento\View\Asset\Service
     */
    protected $assetService;

    /**
     * @var \Magento\View\Publisher
     */
    protected $publisher;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @param Asset\Service $assetService
     * @param Publisher $publisher
     * @param \Magento\View\FileSystem $viewFileSystem
     */
    public function __construct(
        \Magento\View\Asset\Service $assetService,
        \Magento\View\Publisher $publisher,
        \Magento\View\FileSystem $viewFileSystem
    ) {
        $this->assetService = $assetService;
        $this->publisher = $publisher;
        $this->viewFileSystem = $viewFileSystem;
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
        $viewFile = $this->publisher->getViewFile($filePath, $params);
        return $viewFile;
    }
}
