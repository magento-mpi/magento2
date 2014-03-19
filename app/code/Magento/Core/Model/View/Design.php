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
namespace Magento\Core\Model\View;

class Design implements \Magento\View\DesignInterface
{
    /**
     * Package area
     *
     * @var string
     */
    protected $_area;

    /**
     * Package theme
     *
     * @var \Magento\Core\Model\Theme
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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\View\Design\Theme\FlyweightFactory
     */
    protected $_flyweightFactory;

    /**
     * @var \Magento\Core\Model\ThemeFactory
     */
    protected $_themeFactory;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Store\Model\Config
     */
    private $_storeConfig;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_locale;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\View\Design\Theme\FlyweightFactory $flyweightFactory
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Store\Model\ConfigInterface $storeConfig
     * @param \Magento\Core\Model\ThemeFactory $themeFactory
     * @param \Magento\Locale\ResolverInterface $locale
     * @param \Magento\App\State $appState
     * @param array $themes
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\View\Design\Theme\FlyweightFactory $flyweightFactory,
        \Magento\App\ConfigInterface $config,
        \Magento\Store\Model\ConfigInterface $storeConfig,
        \Magento\Core\Model\ThemeFactory $themeFactory,
        \Magento\Locale\ResolverInterface $locale,
        \Magento\App\State $appState,
        array $themes
    ) {
        $this->_storeManager = $storeManager;
        $this->_flyweightFactory = $flyweightFactory;
        $this->_themeFactory = $themeFactory;
        $this->_config = $config;
        $this->_storeConfig = $storeConfig;
        $this->_appState = $appState;
        $this->_themes = $themes;
        $this->_locale = $locale;
    }

    /**
     * Set package area
     *
     * @param string $area
     * @return $this
     * @deprecated
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
        return $this->_appState->getAreaCode();
    }

    /**
     * Set theme path
     *
     * @param \Magento\View\Design\ThemeInterface|string $theme
     * @param string $area
     * @return $this
     */
    public function setDesignTheme($theme, $area = null)
    {
        if ($area) {
            $this->setArea($area);
        } else {
            $area = $this->getArea();
        }

        if ($theme instanceof \Magento\View\Design\ThemeInterface) {
            $this->_theme = $theme;
        } else {
            $this->_theme = $this->_flyweightFactory->create($theme, $area);
        }

        return $this;
    }

    /**
     * Get default theme which declared in configuration
     *
     * Write default theme to core_config_data
     *
     * @param string|null $area
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
     * @return $this
     */
    public function setDefaultDesignTheme()
    {
        $this->setDesignTheme($this->getConfigurationDesignTheme());
        return $this;
    }

    /**
     * Design theme model getter
     *
     * @return \Magento\Core\Model\Theme
     */
    public function getDesignTheme()
    {
        if ($this->_theme === null) {
            $this->_theme = $this->_themeFactory->create();
        }
        return $this->_theme;
    }

    /**
     * {@inheritdoc}
     */
    public function getDesignParams()
    {
        $params = array(
            'area'       => $this->getArea(),
            'themeModel' => $this->getDesignTheme(),
            'locale'     => $this->_locale->getLocaleCode()
        );

        return $params;
    }
}
