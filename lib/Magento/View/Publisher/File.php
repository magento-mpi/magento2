<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

/**
 * Basic publisher file type
 */
class File extends FileAbstract
{
    /**
     * Determine whether a file needs to be published
     *
     * All files located in 'pub/lib' dir should not be published cause it's already publicly accessible.
     * All other files must be processed either if they are not published already (located in 'pub/static'),
     * or if they are css-files and we're working in developer mode.
     *
     * If sourcePath points to file in 'pub/lib' dir - no publishing required
     * If sourcePath points to file in 'pub/static' dir - no publishing required
     *
     * @return bool
     */
    public function isPublicationAllowed()
    {
        if ($this->isPublicationAllowed === null) {
            $filePath = str_replace('\\', '/', $this->sourcePath);
            $this->isPublicationAllowed = !$this->isLibFile($filePath) && !$this->isViewStaticFile($filePath);
        }

        return $this->isPublicationAllowed;
    }

    /**
     * Build unique file path for publication
     *
     * @return string
     */
    public function buildUniquePath()
    {
        if ($this->allowDuplication) {
            $targetPath = $this->buildPublicViewRedundantFilename();
        } else {
            $targetPath = $this->buildPublicViewSufficientFilename();
        }
        return $targetPath;
    }
}
