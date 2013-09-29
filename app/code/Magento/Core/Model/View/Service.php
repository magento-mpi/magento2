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
 * Design service model
 */
class Magento_Core_Model_View_Service
{
    /**
     * Scope separator
     */
    const SCOPE_SEPARATOR = '::';

    /**
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @var Magento_Core_Model_View_DesignInterface
     */
    private $_design;

    /**
     * @var Magento_Core_Model_Theme_FlyweightFactory
     */
    protected $_themeFactory;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * View files system model
     *
     *
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_Theme_FlyweightFactory $themeFactory
     * @param Magento_Core_Model_Dir $dir
     */
    public function __construct(
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_Theme_FlyweightFactory $themeFactory,
        Magento_Core_Model_Dir $dir
    ) {
        $this->_appState = $appState;
        $this->_design = $design;
        $this->_themeFactory = $themeFactory;
        $this->_dir = $dir;
    }

    /**
     * Identify file scope if it defined in file name and override 'module' parameter in $params array
     *
     * It accepts $fileId e.g. Magento_Core::prototype/magento.css and splits it to module part and path part.
     * Then sets module path to $params['module'] and returns path part.
     *
     * @param string $fileId
     * @param array &$params
     * @return string
     * @throws Magento_Exception
     */
    public function extractScope($fileId, array &$params)
    {
        if (preg_match('/\.\//', str_replace('\\', '/', $fileId))) {
            throw new Magento_Exception("File name '{$fileId}' is forbidden for security reasons.");
        }
        if (strpos($fileId, self::SCOPE_SEPARATOR) === false) {
            $file = $fileId;
        } else {
            $fileId = explode(self::SCOPE_SEPARATOR, $fileId);
            if (empty($fileId[0])) {
                throw new Magento_Exception('Scope separator "::" cannot be used without scope identifier.');
            }
            $params['module'] = $fileId[0];
            $file = $fileId[1];
        }
        return $file;
    }

    /**
     * Verify whether we should work with files
     *
     * @return bool
     */
    public function isViewFileOperationAllowed()
    {
        return $this->getAppMode() != Magento_Core_Model_App_State::MODE_PRODUCTION;
    }

    /**
     * Return whether developer mode is turned on
     *
     * @return string
     */
    public function getAppMode()
    {
        return $this->_appState->getMode();
    }

    /**
     * Return directory for theme files publication
     *
     * @return string
     */
    public function getPublicDir()
    {
        return $this->_dir->getDir(Magento_Core_Model_Dir::STATIC_VIEW);
    }

    /**
     * Update required parameters with default values if custom not specified
     *
     * @param array $params
     * @return $this
     */
    public function updateDesignParams(array &$params)
    {
        $defaults = $this->_design->getDesignParams();

        // Set area
        if (empty($params['area'])) {
            $params['area'] = $defaults['area'];
        }

        // Set themeModel
        $theme = null;
        $area = $params['area'];
        if (!empty($params['themeId'])) {
            $theme = $params['themeId'];
        } elseif (isset($params['theme'])) {
            $theme = $params['theme'];
        } elseif (empty($params['themeModel']) && $area !== $defaults['area']) {
            $theme = $this->_design->getConfigurationDesignTheme($area);
        }

        if ($theme) {
            $params['themeModel'] = $this->_themeFactory->create($theme, $area);
        } elseif (empty($params['themeModel'])) {
            $params['themeModel'] = $defaults['themeModel'];
        }


        // Set module
        if (!array_key_exists('module', $params)) {
            $params['module'] = false;
        }

        // Set locale
        if (empty($params['locale'])) {
            $params['locale'] = $defaults['locale'];
        }
        return $this;
    }
}
