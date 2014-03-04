<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\Block\Html\Head;

use Magento\Theme\Block\Html\Head\AssetBlockInterface;
use Magento\RequireJs\Config as RequireJsConfig;

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
     * @var \Magento\RequireJs\Config\File\Manager\Caching
     */
    private $refreshConfigFileStrategy;

    /**
     * @var \Magento\RequireJs\Config\File\Manager\Reuse
     */
    private $reuseConfigFileStrategy;

    /**
     * @param \Magento\View\Element\Context $context
     * @param \Magento\View\Asset\PublicFileFactory $publicAssetFactory
     * @param RequireJsConfig $requirejsConfig
     * @param RequireJsConfig\File\Manager\Refresh $refreshConfigFileStrategy
     * @param RequireJsConfig\File\Manager\Caching $reuseConfigFileStrategy
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        \Magento\View\Asset\PublicFileFactory $publicAssetFactory,
        RequireJsConfig $requirejsConfig,
        RequireJsConfig\File\Manager\Refresh $refreshConfigFileStrategy,
        RequireJsConfig\File\Manager\Caching $reuseConfigFileStrategy,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->refreshConfigFileStrategy = $refreshConfigFileStrategy;
        $this->reuseConfigFileStrategy = $reuseConfigFileStrategy;
        $this->requirejsConfig = $requirejsConfig;
        $this->publicAssetFactory = $publicAssetFactory;
    }

    /**
     * Include RequireJs configuration as an asset on the page
     *
     * @return \Magento\View\Asset\AssetInterface
     */
    public function getAsset()
    {
        $asset = $this->publicAssetFactory->create(array(
            'file'        => $this->getRequireJsConfigManager()->getConfigFile(),
            'contentType' => \Magento\View\Publisher::CONTENT_TYPE_JS,
        ));
        $this->setData('asset', $asset);
        return $asset;
    }

    /**
     * Include base RequireJs configuration necessary for working with Magento application
     *
     * @return string|void
     */
    protected function _toHtml()
    {
        return "<script type=\"text/javascript\">\n"
            . $this->requirejsConfig->getBaseConfig()
            . "</script>\n";
    }

    /**
     * Get RequireJs config manager depending on application mode
     *
     * @return \Magento\RequireJs\Config\File\ManagerInterface
     */
    protected function getRequireJsConfigManager()
    {
        if ($this->_app->isDeveloperMode()) {
            return $this->refreshConfigFileStrategy;
        } else {
            return $this->reuseConfigFileStrategy;
        }
    }
}
