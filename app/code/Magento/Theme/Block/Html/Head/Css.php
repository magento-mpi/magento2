<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block\Html\Head;

/**
 * Css page block
 */
class Css extends \Magento\Framework\View\Element\AbstractBlock implements AssetBlockInterface
{
    /**
     * Get block asset
     *
     * @return \Magento\View\Asset\LocalInterface
     */
    public function getAsset()
    {
        return $this->_assetRepo->createAsset($this->_getData('file'));
    }
}
