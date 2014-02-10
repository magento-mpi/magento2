<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

/**
 * Publisher path builder interface
 */
interface PathBuilderInterface
{
    /**
     * Build published file path
     *
     * @param FileInterface $publisherFile
     * @return string
     */
    public function buildPublishedFilePath(FileInterface $publisherFile);
}
