<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

/**
 * Publisher file type CSS
 */
class CssFile extends File
{
    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Service $viewService
     * @param string $filePath
     * @param array $extension
     * @param array $viewParams
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Service $viewService,
        $filePath,
        $extension,
        array $viewParams
    ) {
        $this->viewService = $viewService;
        parent::__construct($filesystem, $filePath, $extension, $viewParams);
    }

    /**
     * Determine whether a file needs to be published
     *
     * If sourcePath points to CSS file and developer mode is enabled - publish file
     *
     * @return bool
     */
    public function isPublicationAllowed()
    {
        $result = parent::isPublicationAllowed();
        if (!$result) {
            $result = $this->viewService->getAppMode() === \Magento\App\State::MODE_DEVELOPER;
        }
        return $result;
    }
}
