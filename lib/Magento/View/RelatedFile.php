<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * File path resolver
 */
class RelatedFile
{
    /**
     * View service
     *
     * @var Service
     */
    protected $viewService;

    /**
     * View file system
     *
     * @var FileSystem
     */
    protected $viewFileSystem;

    /**
     * @param Service $viewService
     * @param FileSystem $viewFileSystem
     */
    public function __construct(
        Service $viewService,
        FileSystem $viewFileSystem
    ) {
        $this->viewService = $viewService;
        $this->viewFileSystem = $viewFileSystem;
    }

    /**
     * Get relative $fileUrl based on information about parent file path and name.
     *
     * @param string $relativeFilePath URL to the file that was extracted from $parentPath
     * @param string $parentRelativePath original file name identifier that was requested for processing
     * @param array &$params theme/module parameters array
     * @return string
     */
    public function buildPath($relativeFilePath, $parentRelativePath, &$params)
    {
        if (strpos($relativeFilePath, \Magento\View\Service::SCOPE_SEPARATOR)) {
            $relativeFilePath = $this->viewService->extractScope($relativeFilePath, $params);
        } else {
            $relativeFilePath = dirname($parentRelativePath) . '/' . $relativeFilePath;
        }
        return $this->viewFileSystem->normalizePath($relativeFilePath);
    }
}
