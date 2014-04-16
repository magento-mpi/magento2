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
 * Script page block
 */
class Script extends \Magento\Framework\View\Element\AbstractBlock implements AssetBlockInterface
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\View\Asset\ViewFileFactory $viewFileFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Asset\ViewFileFactory $viewFileFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->setAsset(
            $viewFileFactory->create(
                array(
                    'file' => (string)$this->getFile(),
                    'contentType' => \Magento\Framework\View\Publisher::CONTENT_TYPE_JS,
                )
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
        return $this->getData('asset');
    }
}
