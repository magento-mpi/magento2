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

            if ($this->isLibFile($filePath)) {
                $this->isPublicationAllowed = false;
            } elseif (!$this->isViewStaticFile($filePath)) {
                $this->isPublicationAllowed = true;
            } else {
                $this->isPublicationAllowed = $this->viewService->getAppMode() === \Magento\App\State::MODE_DEVELOPER;
            }
        }
        return $this->isPublicationAllowed;
    }
}
