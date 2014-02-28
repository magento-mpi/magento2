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
     * @var \Magento\View\Asset\RemoteFactory
     */
    private $remoteAssetFactory;

    /**
     * @var \Magento\RequireJs\Config
     */
    private $requirejsConfig;

    /**
     * @param \Magento\View\Element\Context|\Magento\View\Element\Template\Context $context
     * @param \Magento\View\Asset\RemoteFactory $remoteFactory
     * @param \Magento\RequireJs\Config $requirejsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\View\Asset\RemoteFactory $remoteFactory,
        \Magento\RequireJs\Config $requirejsConfig,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->remoteAssetFactory = $remoteFactory;
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
        $this->requirejsConfig->crateConfigFile($config);

        $asset = $this->remoteAssetFactory->create(array(
            'url'         => $this->requirejsConfig->getConfigUrl(),
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
