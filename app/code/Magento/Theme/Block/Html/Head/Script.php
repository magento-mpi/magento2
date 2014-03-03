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
class Script extends \Magento\View\Element\AbstractBlock implements AssetBlockInterface
{
    /**
     * @var \Magento\View\Service
     */
    private $viewService;

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
     * @return \Magento\View\Asset\LocalInterface
     */
    public function getAsset()
    {
        return $this->viewService->createAsset($this->_getData('file'));
    }
}
