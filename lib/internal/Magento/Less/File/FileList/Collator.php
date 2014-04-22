<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\File\FileList;

use Magento\Framework\View\File\FileList\CollateInterface;

/**
 * Less file list collator
 */
class Collator implements CollateInterface
{
    /**
     * Collate less files
     *
     * @param \Magento\Framework\View\File[] $files
     * @param \Magento\Framework\View\File[] $filesOrigin
     * @return \Magento\Framework\View\File[]
     */
    public function collate($files, $filesOrigin)
    {
        foreach ($files as $file) {
            $fileId = substr($file->getFileIdentifier(), strpos($file->getFileIdentifier(), '|'));
            foreach (array_keys($filesOrigin) as $identifier) {
                if (false !== strpos($identifier, $fileId)) {
                    unset($filesOrigin[$identifier]);
                }
            }
            $filesOrigin[$file->getFileIdentifier()] = $file;
        }
        return $filesOrigin;
    }
}
