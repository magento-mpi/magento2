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
class Script extends \Magento\View\Element\AbstractBlock implements AssetBlockInterface
{
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\View\Asset\ViewFileFactory $viewFileFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\View\Asset\ViewFileFactory $viewFileFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->setAsset(
            $viewFileFactory->create(
                array('file' => (string)$this->getFile(), 'contentType' => \Magento\View\Publisher::CONTENT_TYPE_JS)
            )
        );
    }

    /**
     * Get block asset
     *
     * @return \Magento\View\Asset\AssetInterface
     */
    public function getAsset()
    {
        return $this->getData('asset');
    }
}
