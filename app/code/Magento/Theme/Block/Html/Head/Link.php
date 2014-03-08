<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Html\Head;

/**
 * Link page block
 */
class Link extends \Magento\View\Element\Template implements AssetBlockInterface
{
    /**
     * Virtual content type
     */
    const VIRTUAL_CONTENT_TYPE = 'link';

    /**
     * Get block asset
     *
     * @return \Magento\View\Asset\AssetInterface
     */
    public function getAsset()
    {
        return $this->_assetService->createRemoteAsset($this->_getData('url'), self::VIRTUAL_CONTENT_TYPE);
    }
}
