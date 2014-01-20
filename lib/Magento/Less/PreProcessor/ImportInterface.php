<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor;

/**
 * Import interface
 */
interface ImportInterface
{
    /**
     * Parse content to find paths that should be pre-processed
     *
     * @param string $lessContent
     * @return $this
     */
    public function generatePaths($lessContent);

    /**
     * Set absolute path to pre-processed file
     *
     * @param string $relativePath
     * @param string $absolutePath
     * @return mixed
     */
    public function setImportPath($relativePath, $absolutePath);

    /**
     * Return relative paths of import instruction
     *
     * @return ImportInterface[]
     */
    public function getImportPaths();
}
