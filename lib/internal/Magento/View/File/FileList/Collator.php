<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\File\FileList;

/**
 * View file list collator
 */
class Collator implements CollateInterface
{
    /**
     * Collate view files
     *
     * @param \Magento\View\File[] $files
     * @param \Magento\View\File[] $filesOrigin
     * @return \Magento\View\File[]
     * @throws \LogicException
     */
    public function collate($files, $filesOrigin)
    {
        foreach ($files as $file) {
            $identifier = $file->getFileIdentifier();
            if (!array_key_exists($identifier, $filesOrigin)) {
                throw new \LogicException(
                    "Overriding view file '{$file->getFilename()}' does not match to any of the files."
                );
            }
            $filesOrigin[$identifier] = $file;
        }
        return $filesOrigin;
    }
}
