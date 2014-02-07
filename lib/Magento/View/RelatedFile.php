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
     * @var Service
     */
    protected $viewService;

    /**
     * @var FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @param Service $viewService
     * @param FileSystem $viewFileSystem
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        Service $viewService,
        FileSystem $viewFileSystem,
        \Magento\Filesystem $filesystem
    ) {
        $this->viewService = $viewService;
        $this->viewFileSystem = $viewFileSystem;
        $this->filesystem = $filesystem;
    }

    /**
     * Get relative $fileUrl based on information about parent file path and name.
     *
     * @param string $relatedFilePath URL to the file that was extracted from $parentPath
     * @param string $parentPath path to the file
     * @param string $parentRelativePath original file name identifier that was requested for processing
     * @param array $params theme/module parameters array
     * @return string
     */
    public function buildPath($relatedFilePath, $parentPath, $parentRelativePath, &$params)
    {
        if (strpos($relatedFilePath, \Magento\View\Service::SCOPE_SEPARATOR)) {
            $filePath = $this->viewService->extractScope(
                $this->viewFileSystem->normalizePath($relatedFilePath),
                $params
            );
        } else {
            /* Check if module file overridden on theme level based on _module property and file path */
            $themesPath = $this->filesystem->getPath(\Magento\App\Filesystem::THEMES_DIR);
            if ($params['module'] && strpos($parentPath, $themesPath) === 0) {
                /* Add module directory to relative URL */
                $filePath = dirname($params['module'] . '/' . $parentRelativePath) . '/' . $relatedFilePath;
                if (strpos($filePath, $params['module']) === 0) {
                    $filePath = ltrim(str_replace($params['module'], '', $filePath), '/');
                } else {
                    $params['module'] = false;
                }
            } else {
                $filePath = dirname($parentRelativePath) . '/' . $relatedFilePath;
            }
        }

        return $this->viewFileSystem->normalizePath($filePath);
    }
}
