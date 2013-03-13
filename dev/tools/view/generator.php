<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     tools
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*

Usage:
 php -f generator.php -- [--dstDir <dir>]

*/

require __DIR__ . '/../../../app/bootstrap.php';
require __DIR__ . '/ThemeProxy.php';

/**
 * Generator of folders where static files should be copied
 */
class Generator
{
    /**
     * @var Mage_Core_Model_Design_Fallback_List_View|null
     */
    private $_fallbackList = null;

    /**
     * @var ThemeProxy[]
     */
    private $_themes = array();

    /**
     * @var string
     */
    private $_destinationDir;

    /**
     * @var bool
     */
    private $_error = false;

    private $_dir;

    /**
     * Constructor
     *
     * @param $argv
     */
    public function __construct($argv)
    {
        foreach ($argv as $key => $value) {
            switch($value) {
                case '--dstDir':
                    $destinationDir = @$argv[$key+1];
                    break;
            }
        }

        $this->_dir = new Mage_Core_Model_Dir(new Magento_Filesystem(new Magento_Filesystem_Adapter_Local), BP);
        if (empty($destinationDir)) {
            $this->_destinationDir = $this->_dir->getDir(Mage_Core_Model_Dir::STATIC_VIEW);
        } else {
            $this->_destinationDir = $destinationDir;
        }

        $this->_initThemes();
        $this->_addDescendantThemes();
        $this->_sortThemes();

        $this->_fallbackList = new Mage_Core_Model_Design_Fallback_List_View($this->_dir);
    }

    /**
     * Main method
     *
     * @return array
     * @throws
     */
    public function run()
    {
        if ($this->_error) {
            return array();
        }
        $params = array(
            'area'          => '<area>',
            'theme_path'    => '<theme_path>',
            'locale'        => '<locale>',
            'pool'          => 'core',
            'namespace'     => '<namespace>',
            'module'        => '<module>',
        );

        $patternDirs = $this->_fallbackList->getPatternDirs('', $params, $this->_themes, false);
        foreach (array_reverse($patternDirs) as $pattern) {
            $globPattern = $this->_fixPattern($pattern['dir']);
            $srcPaths = glob($globPattern);

            foreach ($srcPaths as $src) {
                $paramsFromDir = $this->_getParams(
                    str_replace(BP, '', $src),
                    str_replace(
                        array(BP, '<theme_path>'),
                        array('', '<package>/<theme>'),
                        $pattern['pattern']
                    )
                );

                if (empty($paramsFromDir['theme'])
                        || empty($paramsFromDir['package'])
                        || empty($paramsFromDir['area'])
                ) {
                    $descendants = $this->_themes;
                } else {
                    $themeKey = $paramsFromDir['area'] . '/' . $paramsFromDir['package'] . '/'
                        . $paramsFromDir['theme'];

                    if (!array_key_exists($themeKey, $this->_themes)) {
                        throw InvalidArgumentException("Theme with parameters $themeKey was not found");
                    }
                    $descendants = array_merge(
                        array($this->_themes[$themeKey]),
                        $this->_themes[$themeKey]->getDescendants()
                    );
                }
                if (!empty($paramsFromDir['namespace']) && !empty($paramsFromDir['module'])) {
                    $module = $paramsFromDir['namespace'] . '_' . $paramsFromDir['module'];
                } else {
                    $module = null;
                }
                foreach ($descendants as $theme) {
                    $destination =
                        $this->_getDestinationPath('', $theme->getArea(), $theme->getThemePath(), $module);
                    $this->_copyFiles($src, $destination);
                    $return[] = array($src, $destination);
                }
            }
        }
        return $return;
    }

    /**
     * Copy files
     *
     * @param $sourceDir
     * @param $destinationDir
     */
    protected function _copyFiles($sourceDir, $destinationDir)
    {
        $destinationDir = $destinationDir;
        echo "copy '$sourceDir' to '$destinationDir'\n";
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
        return $this->_destinationDir . DIRECTORY_SEPARATOR . $relPath;
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
                        new ThemeProxy($area, $package . '/' . $theme, $parent);
                }
            }
        }
    }

    /**
     * Add descendant themes
     */
    protected function _addDescendantThemes()
    {
        foreach ($this->_themes as $theme) {
            $rotateTheme = $theme;
            while ($rotateTheme = $rotateTheme->getParentTheme()) {
                $rotateTheme->addDescendantTheme($theme);
            }
        }
    }

    /**
     * Sort themes. Themes without parents go first
     */
    protected function _sortThemes()
    {
        uasort($this->_themes, array($this, '_sortThemesCompare'));
    }

    /**
     * Callback method for sorting themes
     *
     * @param ThemeProxy $theme1
     * @param ThemeProxy $theme2
     * @return int
     */
    protected function _sortThemesCompare($theme1, $theme2)
    {
        if (in_array($theme2, $theme1->getDescendants())) {
            return 1;
        } elseif (in_array($theme1, $theme2->getDescendants())) {
            return -1;
        } else {
            return 0;
        }
    }

    /**
     * Change pattern for using in glob()
     *
     * @param string $pattern
     * @return string mixed
     */
    protected function _fixPattern($pattern)
    {
        return preg_replace("/\<[^\>]+\>/", '*', $pattern);
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

$generator = new Generator($argv);
var_dump($generator->run());
