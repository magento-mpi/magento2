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
     * @var \Magento\View\Service
     */
    private $viewService;

    /**
     * Virtual content type
     */
    const VIRTUAL_CONTENT_TYPE = 'link';

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\View\Service $viewService
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\View\Service $viewService,
        array $data = array()
    ) {
        $this->viewService = $viewService;
        parent::__construct($context, $data);
    }

    /**
     * Get block asset
     *
     * @return \Magento\View\Asset\AssetInterface
     */
    public function getAsset()
    {
        return $this->viewService->createRemoteAsset($this->_getData('url'), self::VIRTUAL_CONTENT_TYPE);
    }
}
