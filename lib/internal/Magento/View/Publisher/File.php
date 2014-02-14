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
     * All files must be processed either if they are not published already (located in 'pub/static'),
     * or if they are css-files and we're working in developer mode.
     *
     * @return bool
     */
    public function isPublicationAllowed()
    {
        if ($this->isPublicationAllowed === null) {
            $filePath = str_replace('\\', '/', $this->sourcePath);
            $this->isPublicationAllowed = !$this->isViewStaticFile($filePath);
        }

        return $this->isPublicationAllowed;
    }
}
