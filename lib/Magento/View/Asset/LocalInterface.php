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
     * Retrieve source file
     *
     * @return string
     */
    public function getSourceFile();
}
