<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\Block\Html\Head;

use Magento\Theme\Block\Html\Head\AssetBlockInterface;

/**
 * Block responsible for including RequireJs config on the page
 */
class Config extends \Magento\View\Element\AbstractBlock implements AssetBlockInterface
{
    /**
     * @var \Magento\View\Asset\PublicFileFactory
     */
    private $publicAssetFactory;

    /**
     * @var \Magento\RequireJs\Config
     */
    private $requirejsConfig;

    /**
     * @param \Magento\View\Element\Context $context
     * @param \Magento\View\Asset\PublicFileFactory $publicAssetFactory
     * @param \Magento\RequireJs\Config $requirejsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        \Magento\View\Asset\PublicFileFactory $publicAssetFactory,
        \Magento\RequireJs\Config $requirejsConfig,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->publicAssetFactory = $publicAssetFactory;
        $this->requirejsConfig = $requirejsConfig;
    }

    /**
     * Include RequireJs configuration as an asset on the page
     *
     * @return \Magento\View\Asset\AssetInterface
     */
    public function getAsset()
    {
        $config = $this->requirejsConfig->getPathsUpdaterJs() . $this->requirejsConfig->getConfig();

        $asset = $this->publicAssetFactory->create(array(
            'file'        => $this->requirejsConfig->crateConfigFile($config),
            'contentType' => \Magento\View\Publisher::CONTENT_TYPE_JS,
        ));
        $this->setData('asset', $asset);
        return $asset;
    }

    /**
     * Include configuration for baseUrl
     *
     * @return string|void
     */
    protected function _toHtml()
    {
        return '<script type="text/javascript">' . PHP_EOL
            . $this->requirejsConfig->getBaseUrlConfig()
            . '</script>' . PHP_EOL;
    }
}
