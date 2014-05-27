<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset;

/**
 * Service model responsible for configuration of minified asset
 */
class MinifyService
{
    /**
     * Config
     *
     * @var ConfigInterface
     */
    protected $config;

    /**
     * ObjectManager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Enabled
     *
     * @var array
     */
    protected $enabled = array();

    /**
     * @var \Magento\Framework\Code\Minifier\AdapterInterface[]
     */
    protected $adapters = array();

    /**
     * @var string
     */
    protected $appMode;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $appMode
     */
    public function __construct(
        ConfigInterface $config,
        \Magento\Framework\ObjectManager $objectManager,
        $appMode = \Magento\Framework\App\State::MODE_DEFAULT
    ) {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->appMode = $appMode;
    }

    /**
     * Get filtered assets
     * Assets applicable for minification are wrapped with the minified asset
     *
     * @param array|\Iterator $assets
     * @return \Magento\Framework\View\Asset\Minified[]
     */
    public function getAssets($assets)
    {
        $resultAssets = array();
        $strategy = $this->appMode == \Magento\Framework\App\State::MODE_PRODUCTION
            ? Minified::FILE_EXISTS : Minified::MTIME;
        /** @var $asset AssetInterface */
        foreach ($assets as $asset) {
            $contentType = $asset->getContentType();
            if ($this->isEnabled($contentType)) {
                /** @var \Magento\Framework\View\Asset\Minified $asset */
                $asset = $this->objectManager
                    ->create(
                        'Magento\Framework\View\Asset\Minified',
                        array(
                            'asset' => $asset,
                            'strategy' => $strategy,
                            'adapter' => $this->getAdapter($contentType),
                        )
                    );
            }
            $resultAssets[] = $asset;
        }
        return $resultAssets;
    }

    /**
     * Check if minification is enabled for specified content type
     *
     * @param string $contentType
     * @return bool
     */
    protected function isEnabled($contentType)
    {
        if (!isset($this->enabled[$contentType])) {
            $this->enabled[$contentType] = $this->config->isAssetMinification($contentType);
        }
        return $this->enabled[$contentType];
    }

    /**
     * Get minification adapter by specified content type
     *
     * @param string $contentType
     * @return \Magento\Framework\Code\Minifier\AdapterInterface
     * @throws \Magento\Framework\Exception
     */
    protected function getAdapter($contentType)
    {
        if (!isset($this->adapters[$contentType])) {
            $adapterClass = $this->config->getAssetMinificationAdapter($contentType);
            if (!$adapterClass) {
                throw new \Magento\Framework\Exception(
                    "Minification adapter is not specified for '$contentType' content type"
                );
            }
            $adapter = $this->objectManager->get($adapterClass);
            if (!($adapter instanceof \Magento\Framework\Code\Minifier\AdapterInterface)) {
                $type = get_class($adapter);
                throw new \Magento\Framework\Exception(
                    "Invalid adapter: '{$type}'. Expected: \\Magento\\Framework\\Code\\Minifier\\AdapterInterface"
                );
            }
            $this->adapters[$contentType] = $adapter;
        }
        return $this->adapters[$contentType];
    }
}
