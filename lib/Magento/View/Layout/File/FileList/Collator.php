<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\FileList;

/**
 * Layout file list collator
 */
class Collator implements CollateInterface
{
    /**
     * Collate layout files
     *
     * @param \Magento\View\Layout\File[] $files
     * @param \Magento\View\Layout\File[] $filesOrigin
     * @return \Magento\View\Layout\File[]
     * @throws \LogicException
     */
    public function collate($files, $filesOrigin)
    {
        foreach ($files as $file) {
            $identifier = $file->getFileIdentifier();
            if (!array_key_exists($identifier, $filesOrigin)) {
                throw new \LogicException(
                    "Overriding layout file '{$file->getFilename()}' does not match to any of the files."
                );
            }
            $filesOrigin[$identifier] = $file;
        }
        return $filesOrigin;
    }
}
