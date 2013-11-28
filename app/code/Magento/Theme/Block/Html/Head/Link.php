<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Html\Head;
use Magento\Theme\Block\Html\Head\AssetBlockInterface;

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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\View\Asset\RemoteFactory $remoteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\View\Asset\RemoteFactory $remoteFactory,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
        $this->setAsset(
            $remoteFactory->create(array(
                'url' => (string)$this->getData('url'),
                'contentType' => self::VIRTUAL_CONTENT_TYPE,
            ))
        );
    }

    /**
     * Get block asset
     *
     * @return \Magento\View\Asset\AssetInterface
     */
    public function getAsset()
    {
        return $this->_getData('asset');
    }
}
