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
     * Check is publication allowed for a file
     *
     * @return bool
     */
    public function isPublicationAllowed();

    /**
     * Build unique file path for publication
     *
     * @return string
     */
    public function buildUniquePath();

    /**
     * Original file extension
     *
     * @return string
     */
    public function getExtension();

    /**
     * @return bool
     */
    public function isSourceFileExists();

    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @return array
     */
    public function getViewParams();

    /**
     * Build path to file located in public folder
     *
     * @return string
     */
    public function buildPublicViewFilename();

    /**
     * Returns absolute path
     *
     * @return string|null
     */
    public function getSourcePath();
}
