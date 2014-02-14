<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * Design service model
 */
class Service
{
    /**
     * Scope separator
     */
    const SCOPE_SEPARATOR = '::';

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\View\DesignInterface
     */
    private $_design;

    /**
     * @var \Magento\View\Design\Theme\FlyweightFactory
     */
    protected $themeFactory;

    /**
     * @var string
     */
    protected $_pubDirectory;

    /**
     * @param \Magento\App\State $appState
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\View\Design\Theme\FlyweightFactory $themeFactory
     * @param \Magento\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\App\State $appState,
        \Magento\View\DesignInterface $design,
        \Magento\View\Design\Theme\FlyweightFactory $themeFactory,
        \Magento\App\Filesystem $filesystem
    ) {
        $this->_appState = $appState;
        $this->_design = $design;
        $this->_pubDirectory = $filesystem->getPath(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $this->themeFactory = $themeFactory;
    }

    /**
     * Identify file scope if it defined in file name and override 'module' parameter in $params array
     *
     * It accepts $fileId e.g. \Magento\Core::prototype/magento.css and splits it to module part and path part.
     * Then sets module path to $params['module'] and returns path part.
     *
     * @param string $fileId
     * @param array &$params
     * @return string
     * @throws \Magento\Exception
     */
    public function extractScope($fileId, array &$params)
    {
        list($module, $file) = self::extractModule($fileId);
        if (!empty($module)) {
            $params['module'] = $module;
        }
        return $file;
    }

    /**
     * Extract module name from specified file ID
     *
     * @param string $fileId
     * @return array
     * @throws \Magento\Exception
     */
    public static function extractModule($fileId)
    {
        if (strpos(str_replace('\\', '/', $fileId), './') !== false) {
            throw new \Magento\Exception("File name '{$fileId}' is forbidden for security reasons.");
        }
        if (strpos($fileId, self::SCOPE_SEPARATOR) === false) {
            return array('', $fileId);
        }
        $result = explode(self::SCOPE_SEPARATOR, $fileId, 2);
        if (empty($fileId[0])) {
            throw new \Magento\Exception('Scope separator "::" cannot be used without scope identifier.');
        }
        return array($result[0], $result[1]);
    }

    /**
     * Verify whether we should work with files
     *
     * @return bool
     */
    public function isViewFileOperationAllowed()
    {
        return $this->getAppMode() != \Magento\App\State::MODE_PRODUCTION;
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
        return $this->_pubDirectory;
    }

    /**
     * Update required parameters with default values if custom not specified
     *
     * @param array $params
     * @throws \UnexpectedValueException
     * @return $this
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
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
            $params['themeModel'] = $this->themeFactory->create($theme, $area);
            if (!$params['themeModel']) {
                throw new \UnexpectedValueException("Could not find theme '$theme' for area '$area'");
            }
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
