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

    /**
     * Build unique file path for publication
     *
     * @return string
     */
    public function getPublicationPath()
    {
        return $this->buildPublicViewRedundantFilename($this->getFilePath(), $this->getViewParams());
    }
}
