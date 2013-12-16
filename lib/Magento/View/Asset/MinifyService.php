<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * Service model responsible for configuration of minified asset
 */
class MinifyService
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $enabled = array();

    /**
     * @var \Magento\Code\Minifier[]
     */
    protected $minifiers = array();

    /**
     * @var \Magento\App\Dir
     */
    protected $dirs;

    /**
     * @var \Magento\App\State
     */
    protected $appState;

    /**
     * @param ConfigInterface $config
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\Dir $dirs
     * @param \Magento\App\State $appState
     */
    public function __construct(
        ConfigInterface $config,
        \Magento\ObjectManager $objectManager,
        \Magento\App\Dir $dirs,
        \Magento\App\State $appState
    ) {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->dirs = $dirs;
        $this->appState = $appState;
    }

    /**
     * Get filtered assets
     * Assets applicable for minification are wrapped with the minified asset
     *
     * @param array|\Iterator $assets
     * @return array
     */
    public function getAssets($assets)
    {
        $resultAssets = array();
        /** @var $asset AssetInterface */
        foreach ($assets as $asset) {
            $contentType = $asset->getContentType();
            if ($this->isEnabled($contentType)) {
                $asset = $this->objectManager
                    ->create('Magento\View\Asset\Minified', array(
                        'asset' => $asset,
                        'minifier' => $this->getMinifier($contentType)
                    ));
            }
            $resultAssets[] = $asset;
        }
        return $resultAssets;
    }

    /**
     * Get minifier object configured with specified content type
     *
     * @param string $contentType
     * @return \Magento\Code\Minifier
     */
    protected function getMinifier($contentType)
    {
        if (!isset($this->minifiers[$contentType])) {
            $adapter = $this->getAdapter($contentType);
            $strategyParams = array(
                'adapter' => $adapter,
            );
            switch ($this->appState->getMode()) {
                case \Magento\App\State::MODE_PRODUCTION:
                    $strategy = $this->objectManager->create('Magento\Code\Minifier\Strategy\Lite', $strategyParams);
                    break;
                default:
                    $strategy = $this->objectManager
                        ->create('Magento\Code\Minifier\Strategy\Generate', $strategyParams);
            }

            $this->minifiers[$contentType] = $this->objectManager->create('Magento\Code\Minifier',
                array(
                    'strategy' => $strategy,
                    'baseDir' => $this->dirs->getDir(\Magento\App\Dir::PUB_VIEW_CACHE) . '/minify',
                )
            );
        }
        return $this->minifiers[$contentType];
    }

    /**
     * Check if minification is enabled for specified content type
     *
     * @param $contentType
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
     * @param $contentType
     * @return mixed
     * @throws \Magento\Exception
     */
    protected function getAdapter($contentType)
    {
        $adapterClass = $this->config->getAssetMinificationAdapter($contentType);
        if (!$adapterClass) {
            throw new \Magento\Exception(
                "Minification adapter is not specified for '$contentType' content type"
            );
        }

        $adapter = $this->objectManager->create($adapterClass);
        return $adapter;
    }
}
