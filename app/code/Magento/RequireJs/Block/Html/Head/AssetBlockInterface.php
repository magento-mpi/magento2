<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RequireJs\Block\Html\Head;

/**
 * Asset block interface
 */
interface AssetBlockInterface
{
    /**
     * Get block asset
     *
     * @return \Magento\Framework\View\Asset\AssetInterface
     */
    public function getAsset();
}
