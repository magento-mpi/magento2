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
     * @var Magento_Filesystem
     */
    private $_filesystem;

    /**
     * @var Mage_Core_Model_Theme_Collection
     */
    private $_themes;

    /**
     * @var Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    private $_fallbackRule;

    /**
     * PCRE matching a named placeholder
     *
     * @var string
     */
    private $_placeholderPcre = '#%(.+?)%#';

    /**
     * Constructor
     *
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Theme_Collection $themes
     * @param Mage_Core_Model_Design_Fallback_Rule_RuleInterface $fallbackRule
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Theme_Collection $themes,
        Mage_Core_Model_Design_Fallback_Rule_RuleInterface $fallbackRule
    ) {
        $this->_filesystem = $filesystem;
        $this->_themes = $themes;
        $this->_fallbackRule = $fallbackRule;
    }

    /**
     * Get rules for copying static view files
     * returns array(
     *      array('source' => <Absolute Source Path>, 'destination' => <Relative Destination Path>),
     *      ......
     * )
     *
     * @return array
     */
    public function getCopyRules()
    {
        $locale = null; // Temporary locale is not taken into account
        $params = array(
            'theme_path'    => $this->_composePlaceholder('theme_path'),
            'locale'        => $locale,
            'namespace'     => $this->_composePlaceholder('namespace'),
            'module'        => $this->_composePlaceholder('module'),
        );
        $result = array();
        /** @var $theme Mage_Core_Model_ThemeInterface */
        foreach ($this->_themes as $theme) {
            $area = $theme->getArea();
            $params['area'] = $area;
            $params['theme'] = $theme;
            $patternDirs = $this->_fallbackRule->getPatternDirs($params);
            foreach (array_reverse($patternDirs) as $pattern) {
                foreach ($this->_getMatchingDirs($pattern) as $srcDir) {
                    $paramsFromDir = $this->_parsePlaceholders($srcDir, $pattern);
                    if (!empty($paramsFromDir['namespace']) && !empty($paramsFromDir['module'])) {
                        $module = $paramsFromDir['namespace'] . '_' . $paramsFromDir['module'];
                    } else {
                        $module = null;
                    }

                    $pathInfo = array(
                        'area' => $area,
                        'locale' => $locale,
                        'themePath' => $theme->getThemePath(),
                        'module' => $module
                    );
                    $destination = $this->_getDestinationPath('', $pathInfo['area'], $pathInfo['themePath'], $module);
                    $destination = rtrim($destination, "\\/");

                    $result[] = array(
                        'source' => $srcDir,
                        'destination' => $destination,
                        'path_info' => $pathInfo
                    );
                }
            }
        }
        return $result;
    }

    /**
     * Compose a named placeholder that does not require escaping when directly used in a PCRE
     *
     * @param string $name
     * @return string
     */
    private function _composePlaceholder($name)
    {
        return '%' . $name . '%';
    }

    /**
     * Retrieve absolute directory paths matching a pattern with placeholders
     *
     * @param string $dirPattern
     * @return array
     */
    private function _getMatchingDirs($dirPattern)
    {
        $patternGlob = preg_replace($this->_placeholderPcre, '*', $dirPattern, -1, $placeholderCount);
        if ($placeholderCount) {
            // autodetect pattern base directory because the filesystem interface requires it
            $firstPlaceholderPos = strpos($patternGlob, '*');
            $patternBaseDir = substr($patternGlob, 0, $firstPlaceholderPos);
            $patternTrailing = substr($patternGlob, $firstPlaceholderPos);
            $paths = $this->_filesystem->searchKeys($patternBaseDir, $patternTrailing);
        } else {
            // pattern is already a valid path containing no placeholders
            $paths = array($dirPattern);
        }
        $result = array();
        foreach ($paths as $path) {
            if ($this->_filesystem->isDirectory($path)) {
                $result[] = $path;
            }
        }
        return $result;
    }

    /**
     * Retrieve placeholder values
     *
     * @param string $subject
     * @param string $pattern
     * @return array
     */
    private function _parsePlaceholders($subject, $pattern)
    {
        $pattern = preg_quote($pattern, '#');
        $parserPcre = '#^' . preg_replace($this->_placeholderPcre, '(?P<\\1>.+?)', $pattern) . '$#';
        if (preg_match($parserPcre, $subject, $placeholders)) {
            return $placeholders;
        }
        return array();
    }

    /**
     * Get relative destination path based on parameters. Calls method used in Production Mode in application
     *
     * @param $filename
     * @param $area
     * @param $themePath
     * @param $module
     * @return string
     */
    private function _getDestinationPath($filename, $area, $themePath, $module)
    {
        return Mage_Core_Model_Design_Package::getPublishedViewFileRelPath($area, $themePath, '', $filename, $module);
    }
}
