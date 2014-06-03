<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block\Html\Head;

/**
 * Script page block
 */
class Script extends \Magento\Framework\View\Element\AbstractBlock implements AssetBlockInterface
{
    /**
     * Get block asset
     *
     * @return \Magento\Framework\View\Asset\LocalInterface
     */
    public function getAsset()
    {
        return $this->_assetRepo->createAsset($this->_getData('file'));
    }
}
