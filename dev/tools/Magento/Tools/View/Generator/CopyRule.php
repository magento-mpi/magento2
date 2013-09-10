<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    view
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Generator of rules which and where folders from code base should be copied
 */
namespace Magento\Tools\View\Generator;

class CopyRule
{
    /**
     * @var \Magento\Filesystem
     */
    private $_filesystem;

    /**
     * @var \Magento_Core_Model_Theme_Collection
     */
    private $_themes;

    /**
     * @var \Magento_Core_Model_Design_Fallback_Rule_RuleInterface
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
     * @param \Magento\Filesystem $filesystem
     * @param \Magento_Core_Model_Theme_Collection $themes
     * @param \Magento_Core_Model_Design_Fallback_Rule_RuleInterface $fallbackRule
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento_Core_Model_Theme_Collection $themes,
        \Magento_Core_Model_Design_Fallback_Rule_RuleInterface $fallbackRule
    ) {
        $this->_filesystem = $filesystem;
        $this->_themes = $themes;
        $this->_fallbackRule = $fallbackRule;
    }

    /**
     * Get rules for copying static view files
     * returns array(
     *      array('source' => <Absolute Source Path>, 'destinationContext' => <Destination Path Context>),
     *      ......
     * )
     *
     * @return array
     */
    public function getCopyRules()
    {
        $result = array();
        /** @var $theme \Magento_Core_Model_ThemeInterface */
        foreach ($this->_themes as $theme) {
            $area = $theme->getArea();
            $nonModularLocations = $this->_fallbackRule->getPatternDirs(array(
                'area'      => $area,
                'theme'     => $theme,
            ));
            $modularLocations = $this->_fallbackRule->getPatternDirs(array(
                'area'      => $area,
                'theme'     => $theme,
                'namespace' => $this->_composePlaceholder('namespace'),
                'module'    => $this->_composePlaceholder('module'),
            ));
            $allDirPatterns = array_merge(
                array_reverse($modularLocations),
                array_reverse($nonModularLocations)
            );
            foreach ($allDirPatterns as $pattern) {
                $pattern = \Magento\Filesystem::fixSeparator($pattern);
                foreach ($this->_getMatchingDirs($pattern) as $srcDir) {
                    $paramsFromDir = $this->_parsePlaceholders($srcDir, $pattern);
                    if (!empty($paramsFromDir['namespace']) && !empty($paramsFromDir['module'])) {
                        $module = $paramsFromDir['namespace'] . '_' . $paramsFromDir['module'];
                    } else {
                        $module = null;
                    }

                    $destinationContext = array(
                        'area' => $area,
                        'themePath' => $theme->getThemePath(),
                        'locale' => null, // Temporary locale is not taken into account
                        'module' => $module
                    );

                    $result[] = array(
                        'source' => $srcDir,
                        'destinationContext' => $destinationContext,
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
}
