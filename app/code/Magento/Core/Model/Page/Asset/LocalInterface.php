<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface of an asset with locally accessible source file
 */
namespace Magento\Core\Model\Page\Asset;

interface LocalInterface extends \Magento\Core\Model\Page\Asset\AssetInterface
{
    /**
     * Retrieve source file
     *
     * @return string
     */
    public function getSourceFile();
}
