<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block\Html\Head;

/**
 * Asset block interface
 */
interface AssetBlock
{
    /**
     * Get block asset
     * @return \Magento\Core\Model\Page\Asset\AssetInterface
     */
    public function getAsset();
}
