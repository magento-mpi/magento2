<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * Interface of an asset with locally accessible source file
 */
interface LocalInterface extends AssetInterface
{
    /**
     * Get original source file where the asset contents can be read from
     *
     * Returns absolute path to file in local file system
     *
     * @return string
     */
    public function getSourceFile();

    /**
     * Get relative path to the asset file
     *
     * This path is an invariant that may be used either for referring to the file in file system or for building URL
     *
     * @return string
     */
    public function getRelativePath();
}
