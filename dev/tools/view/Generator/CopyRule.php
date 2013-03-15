<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Generator of rules which and where folders from code base should be copied
 */
class Generator_CopyRule
{
    /**
     * @var Zend_Log
     */
    private $_logger;

    /**
     * @var Mage_Core_Model_Design_Fallback_List_View|null
     */
    private $_fallbackList = null;

    /**
     * @var Generator_ThemeProxy[]
     */
    private $_themes = array();

    /**
     * @var string
     */
    private $_destinationPath;

    /**
     * @var string
     */
    private $_isDryRun;

    /**
     * @var Mage_Core_Model_Dir
     */
    private $_dirs;

    /**
     * Constructor
     *
     * @param Zend_Log $logger
     * @param array $options
     */
    public function __construct(Zend_Log $logger, $options)
    {
        $this->_logger = $logger;

        $this->_dirs = new Mage_Core_Model_Dir(new Magento_Filesystem(new Magento_Filesystem_Adapter_Local), BP);
        if (empty($options['destination'])) {
            $this->_destinationPath = $this->_dirs->getDir(Mage_Core_Model_Dir::STATIC_VIEW);
        } else {
            $this->_destinationPath = $options['destination'];
        }
        $this->_destinationPath = rtrim($this->_destinationPath, '\\/') . DIRECTORY_SEPARATOR;

        $this->_isDryRun = isset($options['dry-run']);

        $this->_initThemes();

        $this->_fallbackList = new Mage_Core_Model_Design_Fallback_List_View($this->_dirs);
    }

    /**
     * Main method
     */
    public function getCopyRules()
    {
        if ($this->_isDryRun) {
            $this->_log('Running in dry-run mode: no changes are applied');
        }

        $this->_verifyDestinationEmpty();
        $this->_createDestinationIfNeeded();

        $params = array(
            'area'          => '*',
            'theme_path'    => '*',
            'locale'        => null,
            'pool'          => '*',
            'namespace'     => '*',
            'module'        => '*',
        );

        $result = array();
        foreach ($this->_themes as $theme) {
            $params['theme'] = $theme;
            $params['area'] = $theme->getArea();
            $patternDirs = $this->_fallbackList->getPatternDirs($params, false);
            foreach (array_reverse($patternDirs) as $pattern) {
                $srcPaths = glob($pattern['dir']);
                foreach ($srcPaths as $src) {
                    $paramsFromDir = $this->_getParams(
                        str_replace(BP, '', $src),
                        str_replace(
                            array(BP, '<theme_path>'),
                            array('', '<package>/<theme>'),
                            $pattern['pattern']
                        )
                    );
                    if (!empty($paramsFromDir['namespace']) && !empty($paramsFromDir['module'])) {
                        $module = $paramsFromDir['namespace'] . '_' . $paramsFromDir['module'];
                    } else {
                        $module = null;
                    }

                    $result[] = array(
                        $src,
                        $this->_getDestinationPath('', $theme->getArea(), $theme->getThemePath(), $module)
                    );
                }
            }
        }
    }

    /**
     * Check that destination is empty, as we'd better not mess different files together
     *
     * @throws Magento_Exception
     */
    protected function _verifyDestinationEmpty()
    {
        if (glob($this->_destinationPath . DIRECTORY_SEPARATOR . '*')) {
            throw new Magento_Exception("The destination path {$this->_destinationPath} must be empty");
        }
    }

    /**
     * Create destination dir if needed
     *
     * @throws Magento_Exception
     */
    protected function _createDestinationIfNeeded()
    {
        if ($this->_isDryRun || is_dir($this->_destinationPath)) {
            return;
        }
        if (!@mkdir($this->_destinationPath, 0666, true)) {
            throw new Magento_Exception("Unable to create destination path {$this->_destinationPath}");
        }
    }

    /**
     * Log message
     *
     * @param string $message
     * @param int $priority
     */
    protected function _log($message, $priority = Zend_Log::INFO)
    {
        $this->_logger->log($message, $priority);
    }

    /**
     * Function from Magento
     *
     * @param $filename
     * @param $area
     * @param $themePath
     * @param $module
     * @return string
     */
    protected function _getDestinationPath($filename, $area, $themePath, $module)
    {
        $relPath = Mage_Core_Model_Design_Package::getPublishedViewFileRelPath($area, $themePath, '', $filename,
            $module);
        return $this->_destinationPath . $relPath;
    }

    /**
     * Get themes from file system
     */
    protected function _initThemes()
    {
        $themesDir = BP . '/app/design';
        $dir = dir($themesDir);
        while (false !== ($area = $dir->read())) {
            if ($area == '..' || $area == '.') {
                continue;
            }
            $dirArea = dir($themesDir . DS . $area);
            while (false !== ($package = $dirArea->read())) {
                if ($package == '..' || $package == '.') {
                    continue;
                }
                $dirPackage = dir($themesDir . DS . $area . DS . $package);
                while (false !== ($theme = $dirPackage->read())) {
                    if ($theme == '..' || $theme == '.') {
                        continue;
                    }
                    $themeConfig =
                        simplexml_load_file($themesDir . DS . $area . DS . $package . DS . $theme . DS . 'theme.xml');
                    $themeInfo = $themeConfig->xpath('/design/package/theme');

                    if (isset($themeInfo[0]['parent'])) {
                        $parent = $package . '/' . (string)$themeInfo[0]['parent'];
                    } else {
                        $parent = null;
                    }
                    $this->_themes[$area . '/' . $package . '/' . $theme] =
                        new Generator_ThemeProxy($area, $package . '/' . $theme, $parent);
                }
            }
        }
    }

    /**
     * Extract params from $path using $pattern
     *
     * @param string $path
     * @param string $pattern
     * @return array
     */
    protected function _getParams($path, $pattern)
    {
        $path = str_replace(DS, '/', $path);
        $pattern = str_replace(DS, '/', $pattern);
        $params = explode('/', $pattern);
        $pathParts = explode('/', $path);
        $result = array();
        foreach ($params as $k => $param) {
            if (!isset($pathParts[$k])) {
                $result = array();
                break;
            }
            if (!preg_match("/^\<.+\>$/", $param)) {
                continue;
            }
            $param = ltrim($param, '<');
            $param = rtrim($param, '>');
            if (preg_match("/\>_\</", $param)) {
                $pathSubParts = explode('_', $pathParts[$k]);
                $subParams = explode('>_<', $param);
                if (count($subParams) == count($pathSubParts)) {
                    foreach ($subParams as $j => $subParam) {
                        $result[$subParam] = $pathSubParts[$j];
                    }
                } else {
                    $result = array();
                    break;
                }
            } else {
                $result[$param] = $pathParts[$k];
            }
        }
        return $result;
    }
}
