<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Service model responsible for configuration of minified asset
 */
class Mage_Core_Model_Page_Asset_MinifyService
{
    /**
     * @var Mage_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_enabled = array();

    /**
     * @var Magento_Code_Minifier[]|bool[]
     */
    protected $_minifiers = array();

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var Mage_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Mage_Core_Model_Store_Config $config
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_App_State $appState
     */
    public function __construct(
        Mage_Core_Model_Store_Config $config,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_App_State $appState
    ) {
        $this->_storeConfig = $config;
        $this->_objectManager = $objectManager;
        $this->_dirs = $dirs;
        $this->_appState = $appState;
    }

    /**
     * Get filtered assets
     * Assets applicable for minification are wrapped with the minified asset
     *
     * @param array|Iterator $assets
     * @return array
     */
    public function getAssets($assets)
    {
        $resultAssets = array();
        /** @var $asset Mage_Core_Model_Page_Asset_AssetInterface */
        foreach ($assets as $asset) {
            $contentType = $asset->getContentType();
            if ($this->_isEnabled($contentType)) {
                $resultAssets[] = $this->_objectManager
                    ->create('Mage_Core_Model_Page_Asset_Minified', array(
                        'asset' => $asset,
                        'minifier' => $this->_getMinifier($contentType)
                    ));
            } else {
                $resultAssets[] = $asset;
            }
        }
        return $resultAssets;
    }

    /**
     * Get minifier object configured with specified content type
     *
     * @param string $contentType
     * @return bool|Magento_Code_Minifier
     */
    protected function _getMinifier($contentType)
    {
        if (!isset($this->_minifiers[$contentType])) {
            $adapter = $this->_getAdapter($contentType);
            $strategyParams = array(
                'adapter' => $adapter,
                'baseDir' => $this->_dirs->getDir(Mage_Core_Model_Dir::PUB_VIEW_CACHE) . '/minify',
            );
            switch ($this->_appState->getMode()) {
                case Mage_Core_Model_App_State::MODE_PRODUCTION:
                    $strategy = $this->_objectManager->create('Magento_Code_Minify_Strategy_Lite', $strategyParams);
                    break;
                default:
                    $strategy = $this->_objectManager
                        ->create('Magento_Code_Minify_Strategy_Generate', $strategyParams);
            }

            $this->_minifiers[$contentType] = $this->_objectManager->create('Magento_Code_Minifier',
                array('strategy' => $strategy));
        }
        return $this->_minifiers[$contentType];
    }

    /**
     * Check if minification is enabled for specified content type
     *
     * @param $contentType
     * @return bool
     */
    protected function _isEnabled($contentType)
    {
        if (!isset($this->_enabled[$contentType])) {
            $this->_enabled[$contentType] = $this->_storeConfig->getConfigFlag('dev/' . $contentType . '/minify');
        }
        return $this->_enabled[$contentType];
    }

    /**
     * Get minification adapter by specified content type
     *
     * @param $contentType
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _getAdapter($contentType)
    {
        $adapterClass = $this->_storeConfig->getConfig('dev/' . $contentType . '/minify_adapter');
        if (!$adapterClass) {
            throw new Mage_Core_Exception(
                "Minification adapter is not specified for '$contentType' content type"
            );
        }

        $adapter = $this->_objectManager->create($adapterClass);
        return $adapter;
    }
}
