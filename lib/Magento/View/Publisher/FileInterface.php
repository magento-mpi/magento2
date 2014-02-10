<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

/**
 * Publisher file interface
 */
interface FileInterface
{
    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @return string
     */
    public function getExtension();

    /**
     * @return array
     */
    public function getViewParams();

    /**
     * Check is publication allowed for a file
     *
     * @return bool
     */
    public function isPublicationAllowed();

    /**
     * @param string $sourcePath
     * @return $this
     */
    public function setSourcePath($sourcePath);

    /**
     * @return string|null
     */
    public function getSourcePath();

    /**
     * Build unique file path for publication
     *
     * @return string
     */
    public function getPublicationPath();
}
