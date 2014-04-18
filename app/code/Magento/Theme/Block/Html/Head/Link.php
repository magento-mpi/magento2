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
class Link extends \Magento\Framework\View\Element\Template implements AssetBlockInterface
{
    /**
     * Virtual content type
     */
    const VIRTUAL_CONTENT_TYPE = 'link';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\View\Asset\RemoteFactory $remoteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Asset\RemoteFactory $remoteFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->setAsset(
            $remoteFactory->create(
                array('url' => (string)$this->getData('url'), 'contentType' => self::VIRTUAL_CONTENT_TYPE)
            )
        );
    }

    /**
     * Get block asset
     *
     * @return \Magento\Framework\View\Asset\AssetInterface
     */
    public function getAsset()
    {
        return $this->_getData('asset');
    }
}
