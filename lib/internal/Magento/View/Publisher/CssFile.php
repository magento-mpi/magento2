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
class CssFile extends FileAbstract
{
    /**
     * @var \Magento\View\Service
     */
    private $viewService;

    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\Module\Dir\Reader $modulesReader,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\View\Asset\PathGenerator $path,
        \Magento\View\Service $viewService,
        $filePath,
        array $viewParams,
        $sourcePath = null
    ) {
        $this->viewService = $viewService;
        parent::__construct($filesystem, $modulesReader, $viewFileSystem, $path, $filePath, $viewParams, $sourcePath);
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
        if ($this->isPublicationAllowed === null) {
            $filePath = str_replace('\\', '/', $this->sourcePath);

            if (!$this->isViewStaticFile($filePath)) {
                $this->isPublicationAllowed = true;
            } else {
                $this->isPublicationAllowed = $this->viewService->isPublishingDisallowed();
            }
        }
        return $this->isPublicationAllowed;
    }

    /**
     * Restore view service object at unserialization
     */
    public function __wakeup()
    {
        $objectManager = \Magento\App\ObjectManager::getInstance();
        $this->viewService = $objectManager->get('\Magento\View\Service');
        parent::__wakeup();
    }
}
