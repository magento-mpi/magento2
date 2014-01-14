<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\LibraryLoader;

/**
 * Library loader interface
 */
interface LoaderInterface
{
    /**
     * Include library files
     */
    public function load();
}
