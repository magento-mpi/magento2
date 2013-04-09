<?php
/**
 * Test constructions of layout files
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * This test finds usages of modular view files, searched in non-modular context - it is obsolete and buggy
 * functionality, introduced in Magento 2.
 *
 * The test goes through modular calls of view files, and finds out, whether there are theme non-modular files
 * with the same path. Before fixing the bug, such call return theme files instead of  modular files, which is
 * incorrect. After fixing the bug, such calls will start returning modular files, which is not a file we got used
 * to see, so such cases are probably should be fixed.
 *
 * The test is intended to be deleted before Magento 2 release. With the release, having non-modular files with the
 * same paths as modular ones, is legitimate.
 */
class Integrity_Modular_FallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_FileResolution_Strategy_Fallback
     */
    static protected $_fallback;

    /**
     * @var array
     */
    static protected $_locales;

    /**
     * @var array
     */
    static protected $_themes;

    public static function setUpBeforeClass()
    {
        $objectManager = Mage::getObjectManager();
        self::$_fallback = $objectManager->get('Mage_Core_Model_Design_FileResolution_Strategy_Fallback');

        // Locales to be checked
        $localeModel = new Zend_Locale;
        self::$_locales = array_keys($localeModel->getLocaleList());
        self::$_locales[] = null;
        self::$_locales = array(null);// FIXME

        // Themes to be checked
        /** @var $themeCollection Mage_Core_Model_Theme_Collection */
        $themeCollection = $objectManager->get('Mage_Core_Model_Theme_Collection');
        $themeCollection->addDefaultPattern('*');
        foreach ($themeCollection as $theme) {
            self::$_themes[] = $theme;
        }

        // Add empty theme, so we surely reach the final fallback level
        $themeCollection->setBaseDir(__DIR__ . '/_files/fallback_tests/empty_theme')
            ->addDefaultPattern('*');
        foreach ($themeCollection as $theme) {
            self::$_themes[] = $theme;
        }
    }

    /**
     * @param string $modularCall
     * @param array $usages
     * @param null|string $area
     * @dataProvider modularFallbackDataProvider
     */
    public function testModularFallback($modularCall, array $usages, $area)
    {
        list(, $file) = explode(Mage_Core_Model_Design_Package::SCOPE_SEPARATOR, $modularCall);

        $wrongResolutions = array();
        foreach (self::$_themes as $theme) {
            if ($area && ($theme->getArea() != $area)) {
                continue;
            }

            foreach (self::$_locales as $locale) {
                $found = $this->_getFileResolutions($theme, $locale, $file);
                $wrongResolutions = array_merge($wrongResolutions, $found);
            }
        }

        if ($wrongResolutions) {
            // If file is found, then old functionality (find modular files in non-modular locations) is used
            $message = sprintf(
                "Found modular call:\n  %s in\n  %s\n  which may resolve to non-modular location(s):\n  %s",
                $modularCall,
                implode(', ', $usages),
                implode(', ', $wrongResolutions)
            );
            $this->fail($message);
        }
    }

    /**
     * Resolves file to find its paths, how it can be resolved
     *
     * @param Mage_Core_Model_Theme $theme
     * @param string $locale
     * @param string $file
     * @return array
     */
    protected function _getFileResolutions(Mage_Core_Model_Theme $theme, $locale, $file)
    {
        $found = array();
        $fileResolved = self::$_fallback->getFile($theme->getArea(), $theme, $file);
        if (file_exists($fileResolved)) {
            $found[$fileResolved] = $fileResolved;
        }

        $fileResolved = self::$_fallback->getViewFile($theme->getArea(), $theme, $locale, $file);
        if (file_exists($fileResolved)) {
            $found[$fileResolved] = $fileResolved;
        }

        return $found;
    }

    /**
     * @return array
     */
    public static function modularFallbackDataProvider()
    {
        $blackListPaths = self::_getBlackListPaths();

        $result = array();
        $root = self::_getRootDir();
        $directory = new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory);
        foreach ($iterator as $file) {
            $file = (string) $file;

            if (self::_isDirectoryBlacklisted($file, $blackListPaths)) {
                continue;
            }

            // Try to find modular context
            $modulePattern = '[A-Z][a-z]+_[A-Z][a-z]+';
            $filePattern = '[[:alnum:]_/-]+\\.[[:alnum:]_./-]+';
            $pattern = '#' . $modulePattern . Mage_Core_Model_Design_Package::SCOPE_SEPARATOR . $filePattern . '#';
            if (!preg_match_all($pattern, file_get_contents($file), $matches)) {
                continue;
            }

            $area = self::_getArea($file);

            foreach ($matches[0] as $modularCall) {
                $key = $modularCall . ' @ ' . ($area ?: 'any area');

                if (!isset($result[$key])) {
                    $result[$key] = array(
                        'modularCall' => $modularCall,
                        'usages' => array(),
                        'area' => $area
                    );
                }
                $result[$key]['usages'][$file] = $file;
            }
        }
        return $result;
    }

    /**
     * Return paths, that we should not look into for optimization purposes
     *
     * @return array
     */
    protected static function _getBlackListPaths()
    {
        $root = self::_getRootDir();
        $result = array(
            $root . '/.git',
            $root . '/var',                                                    // Application cache
            __DIR__,                                                           // Hardcoded values in some modular tests
            Magento_Test_Helper_Bootstrap::getInstance()->getAppInstallDir(),  // Integration tests cache
        );

        foreach ($result as $key => $dir) {
            $result[$key] = str_replace('/', DIRECTORY_SEPARATOR, $dir . '/');
        }

        return $result;
    }

    /**
     * Returns application root directory
     *
     * @return string
     */
    static protected function _getRootDir()
    {
        return realpath(__DIR__ . '/../../../../../../');
    }

    /**
     * Test, whether file is in a blacklisted directory
     *
     * @param string $file
     * @param array $blackListPaths
     * @return bool
     */
    protected static function _isDirectoryBlacklisted($file, $blackListPaths)
    {
        $file .= DIRECTORY_SEPARATOR;
        foreach ($blackListPaths as $path) {
            if (substr($file, 0, strlen($path)) == $path) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the area, where file is located.
     *
     * Null is returned, if the file is not within an area, e.g. it is a model/block/helper php-file.
     *
     * @param string $file
     * @return string|null
     */
    protected static function _getArea($file)
    {
        $file = str_replace(DIRECTORY_SEPARATOR, '/', $file);
        $areaPatterns = array(
            '#app/code/[^/]+/[^/]+/view/([^/]+)/#',
            '#app/design/([^/]+)/#',
        );
        foreach ($areaPatterns as $pattern) {
            if (preg_match($pattern, $file, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
