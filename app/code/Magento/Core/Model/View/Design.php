<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Keeps design settings for current request
 */
class Magento_Core_Model_View_Design implements Magento_Core_Model_View_DesignInterface
{
    /**
     * Common node path to theme design configuration
     */
    const XML_PATH_THEME_ID = 'design/theme/theme_id';

    /**
     * Regular expressions matches cache
     *
     * @var array
     */
    private static $_regexMatchCache      = array();

    /**
     * Custom theme type cache
     *
     * @var array
     */
    private static $_customThemeTypeCache = array();

    /**
     * Package area
     *
     * @var string
     */
    protected $_area;

    /**
     * Package theme
     *
     * @var Magento_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Directory of the css file
     * Using only to transmit additional parameter in callback functions
     *
     * @var string
     */
    protected $_callbackFileDir;

    /**
     * Store list manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Theme_FlyweightFactory
     */
    protected $_flyweightFactory;

    /**
     * @var Magento_Core_Model_Theme
     */
    protected $_themeFactory;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_Store_Config
     */
    private $_storeConfig;

    /**
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Theme_FlyweightFactory $flyweightFactory
     * @param Magento_Core_Model_ConfigInterface $config
     * @param Magento_Core_Model_Store_ConfigInterface $storeConfig
     * @param Magento_Core_Model_ThemeFactory $themeFactory
     * @param Magento_Core_Model_App $app
     * @param array $themes
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Theme_FlyweightFactory $flyweightFactory,
        Magento_Core_Model_ConfigInterface $config,
        Magento_Core_Model_Store_ConfigInterface $storeConfig,
        Magento_Core_Model_ThemeFactory $themeFactory,
        Magento_Core_Model_App $app,
        array $themes
    ) {
        $this->_storeManager = $storeManager;
        $this->_flyweightFactory = $flyweightFactory;
        $this->_themeFactory = $themeFactory;
        $this->_config = $config;
        $this->_storeConfig = $storeConfig;
        $this->_themes = $themes;
        $this->_app = $app;
    }

    /**
     * Set package area
     *
     * @param string $area
     * @return Magento_Core_Model_View_Design
     */
    public function setArea($area)
    {
        $this->_area = $area;
        $this->_theme = null;
        return $this;
    }

    /**
     * Retrieve package area
     *
     * @return string
     */
    public function getArea()
    {
        if (is_null($this->_area)) {
            $this->_area = self::DEFAULT_AREA;
        }
        return $this->_area;
    }

    /**
     * Set theme path
     *
     * @param Magento_Core_Model_Theme|int|string $theme
     * @param string $area
     * @return Magento_Core_Model_View_Design
     */
    public function setDesignTheme($theme, $area = null)
    {
        if ($area) {
            $this->setArea($area);
        }

        if ($theme instanceof Magento_Core_Model_Theme) {
            $this->_theme = $theme;
        } else {
            $this->_theme = $this->_flyweightFactory->create($theme, $this->getArea());
        }

        return $this;
    }

    /**
     * Get default theme which declared in configuration
     *
     * Write default theme to core_config_data
     *
     * @param string $area
     * @param array $params
     * @return string|int
     */
    public function getConfigurationDesignTheme($area = null, array $params = array())
    {
        if (!$area) {
            $area = $this->getArea();
        }

        $theme = null;
        $store = isset($params['store']) ? $params['store'] : null;

        if ($this->_isThemePerStoveView($area)) {
            $theme = $this->_storeManager->isSingleStoreMode()
                ? $this->_config->getValue(self::XML_PATH_THEME_ID, 'default')
                : (string)$this->_storeConfig->getConfig(self::XML_PATH_THEME_ID, $store);
        }

        if (!$theme && isset($this->_themes[$area])) {
            $theme = $this->_themes[$area];
        }

        return $theme;
    }

    /**
     * Whether themes in specified area are supposed to be configured per store view
     *
     * @param string $area
     * @return bool
     */
    private function _isThemePerStoveView($area)
    {
        return $area == self::DEFAULT_AREA;
    }

    /**
     * Set default design theme
     *
     * @return Magento_Core_Model_View_Design
     */
    public function setDefaultDesignTheme()
    {
        $this->setDesignTheme($this->getConfigurationDesignTheme());
        return $this;
    }

    /**
     * Design theme model getter
     *
     * @return Magento_Core_Model_Theme
     */
    public function getDesignTheme()
    {
        if ($this->_theme === null) {
            $this->_theme = $this->_themeFactory->create();
        }
        return $this->_theme;
    }

    /**
     * Return package name based on design exception rules
     *
     * @param array $rules - design exception rules
     * @param string $regexpsConfigPath
     * @return bool|string
     */
    public static function getPackageByUserAgent(array $rules, $regexpsConfigPath = 'path_mock')
    {
        foreach ($rules as $rule) {
            if (!empty(self::$_regexMatchCache[$rule['regexp']][$_SERVER['HTTP_USER_AGENT']])) {
                self::$_customThemeTypeCache[$regexpsConfigPath] = $rule['value'];
                return $rule['value'];
            }

            $regexp = '/' . trim($rule['regexp'], '/') . '/';

            if (@preg_match($regexp, $_SERVER['HTTP_USER_AGENT'])) {
                self::$_regexMatchCache[$rule['regexp']][$_SERVER['HTTP_USER_AGENT']] = true;
                self::$_customThemeTypeCache[$regexpsConfigPath] = $rule['value'];
                return $rule['value'];
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDesignParams()
    {
        $params = array(
            'area'       => $this->getArea(),
            'themeModel' => $this->getDesignTheme(),
            'locale'     => $this->_app->getLocale()->getLocaleCode()
        );

        return $params;
    }
}
