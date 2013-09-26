<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Navigation mode design editor url model
 */
class Magento_DesignEditor_Model_Url_NavigationMode extends Magento_Core_Model_Url
{
    /**
     * VDE helper
     *
     * @var Magento_DesignEditor_Helper_Data
     */
    protected $_helper;

    /**
     * Current mode in design editor
     *
     * @var string
     */
    protected $_mode;

    /**
     * Current editable theme id
     *
     * @var int
     */
    protected $_themeId;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Url_SecurityInfoInterface $securityInfo
     * @param Magento_DesignEditor_Helper_Data $helper
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Session $session
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Url_SecurityInfoInterface $securityInfo,
        Magento_DesignEditor_Helper_Data $helper,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_App $app,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Session $session,
        array $data = array()
    ) {
        $this->_helper = $helper;
        if (isset($data['mode'])) {
            $this->_mode = $data['mode'];
        }

        if (isset($data['themeId'])) {
            $this->_themeId = $data['themeId'];
        }
        parent::__construct($securityInfo, $coreStoreConfig, $coreData, $app, $storeManager, $session, $data);
    }

    /**
     * Retrieve route URL
     *
     * @param string $routePath
     * @param array $routeParams
     * @return string
     */
    public function getRouteUrl($routePath = null, $routeParams = null)
    {
        $this->_hasThemeAndMode();
        $url = parent::getRouteUrl($routePath, $routeParams);
        $baseUrl = trim($this->getBaseUrl(), '/');
        $vdeBaseUrl = implode('/', array(
            $baseUrl, $this->_helper->getFrontName(), $this->_mode, $this->_themeId
        ));
        if (strpos($url, $baseUrl) === 0 && strpos($url, $vdeBaseUrl) === false) {
            $url = str_replace($baseUrl, $vdeBaseUrl, $url);
        }
        return $url;
    }

    /**
     * Verifies is theme and mode were set or not
     *
     * Ugly hack to make it possible to cover class with unit test
     *
     * @return $this
     */
    protected function _hasThemeAndMode()
    {
        if (!$this->_mode) {
            $this->_mode = $this->getRequest()->getAlias('editorMode');
        }

        if (!$this->_themeId) {
            $this->_themeId = $this->getRequest()->getAlias('themeId');
        }
        return $this;
    }
}
