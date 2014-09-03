<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\Block\Html\Head;

/**
 * Block responsible for including RequireJs config on the page
 */
class Config extends \Magento\Framework\View\Element\AbstractBlock implements AssetBlockInterface
{
    /**
     * @var \Magento\Framework\RequireJs\Config
     */
    private $config;

    /**
     * @var \Magento\RequireJs\Model\FileManager
     */
    private $fileManager;

    /**
     * @var \Magento\Framework\View\Asset\LocalInterface
     */
    private $asset;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\RequireJs\Config $config
     * @param \Magento\RequireJs\Model\FileManager $fileManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\RequireJs\Config $config,
        \Magento\RequireJs\Model\FileManager $fileManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->fileManager = $fileManager;
    }

    /**
     * Include RequireJs configuration as an asset on the page
     *
     * @return \Magento\Framework\View\Asset\LocalInterface
     */
    public function getAsset()
    {
        if (!$this->asset) {
            $this->asset = $this->fileManager->createRequireJsAsset();
        }
        return $this->asset;
    }

    /**
     * Include base RequireJs configuration necessary for working with Magento application
     *
     * @return string|void
     */
    protected function _toHtml()
    {
        return "<script type=\"text/javascript\">\n"
            . $this->config->getBaseConfig()
            . "</script>\n";
    }
}
