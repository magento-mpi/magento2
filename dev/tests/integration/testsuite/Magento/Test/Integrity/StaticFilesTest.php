<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity;

/**
 * An integrity test that searches for references to static files and asserts that they are resolved via fallback
 */
class StaticFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Design\FileResolution\Fallback\StaticFile
     */
    private $fallback;

    /**
     * @var \Magento\Framework\View\Design\FileResolution\Fallback\Resolver\Simple
     */
    private $explicitFallback;

    /**
     * @var \Magento\Framework\View\Design\Theme\FlyweightFactory
     */
    private $themeRepo;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    private $design;

    protected function setUp()
    {
        $om = \Magento\TestFramework\Helper\Bootstrap::getObjectmanager();
        $this->fallback = $om->get('Magento\Framework\View\Design\FileResolution\Fallback\StaticFile');
        $this->explicitFallback = $om->get('Magento\Framework\View\Design\FileResolution\Fallback\Resolver\Simple');
        $this->themeRepo = $om->get('Magento\Framework\View\Design\Theme\FlyweightFactory');
        $this->design = $om->get('Magento\Framework\View\DesignInterface');
    }

    /**
     * Scan references to files from other static files and assert they are correct
     *
     * The CSS or LESS files may refer to other resources using @import or url() notation
     * We want to check integrity of all these references
     * Note that the references may have syntax specific to the Magento preprocessing subsystem
     *
     * @param string $absolutePath
     * @param string $area
     * @param string $themePath
     * @param string $locale
     * @param string $module
     * @param string $filePath
     * @dataProvider referencesFromStaticFilesDataProvider
     */
    public function testReferencesFromStaticFiles($area, $themePath, $locale, $module, $filePath, $absolutePath)
    {
        $contents = file_get_contents($absolutePath);
        preg_match_all(
            \Magento\Framework\View\Url\CssResolver::REGEX_CSS_RELATIVE_URLS,
            $contents,
            $matches
        );
        foreach ($matches[1] as $relatedResource) {
            if (false !== strpos($relatedResource, '@')) { // unable to parse paths with LESS variables/mixins
                continue;
            }
            list($relatedModule, $relatedPath) =
                \Magento\Framework\View\Asset\Repository::extractModule($relatedResource);
            if ($relatedModule) {
                $fallbackModule = $relatedModule;
            } else {
                if ('less' == pathinfo($filePath, PATHINFO_EXTENSION)) {
                    /**
                     * The LESS library treats the related resources with relative links not in the same way as CSS:
                     * when another LESS file is included, it is embedded directly into the resulting document, but the
                     * relative paths of related resources are not adjusted accordingly to the new root file.
                     * Probably it is a bug of the LESS library.
                     */
                    $this->markTestSkipped("Due to LESS library specifics, the '{$relatedResource}' cannot be tested.");
                }
                $fallbackModule = $module;
                $relatedPath = \Magento\Framework\View\FileSystem::getRelatedPath($filePath, $relatedResource);

            }
            // the $relatedPath will be suitable for feeding to the fallback system
            $this->assertNotEmpty(
                $this->getStaticFile($area, $themePath, $locale, $relatedPath, $fallbackModule),
                "The related resource cannot be resolved through fallback: '{$relatedResource}'"
            );
        }
    }

    /**
     * Get a default theme path for specified area
     *
     * @param string $area
     * @return string
     * @throws \LogicException
     */
    private function getDefaultThemePath($area)
    {
        switch ($area) {
            case 'frontend':
                return $this->design->getConfigurationDesignTheme($area);
            case 'adminhtml':
                return 'Magento/backend';
            case 'install':
                return 'Magento/basic';
            case 'doc':
                return 'Magento/blank';
            default:
                throw new \LogicException('Unable to determine theme path');
        }
    }

    /**
     * Get static file through fallback system using specified params
     *
     * @param string $area
     * @param string|\Magento\Framework\View\Design\ThemeInterface $theme - either theme path (string) or theme object
     * @param string $locale
     * @param string $filePath
     * @param string $module
     * @param bool $isExplicit
     * @return bool|string
     */
    private function getStaticFile($area, $theme, $locale, $filePath, $module, $isExplicit = false)
    {
        if (!is_object($theme)) {
            $themePath = $theme ?: $this->getDefaultThemePath($area);
            $theme = $this->themeRepo->create($themePath, $area);
        }
        if ($isExplicit) {
            $type = \Magento\Framework\View\Design\Fallback\RulePool::TYPE_STATIC_FILE;
            return $this->explicitFallback->resolve($type, $filePath, $area, $theme, $locale, $module);
        }
        return $this->fallback->getFile($area, $theme, $locale, $filePath, $module);
    }

    /**
     * @return array
     */
    public function referencesFromStaticFilesDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getStaticPreProcessingFiles('*.{less,css}');
    }

    /**
     * There must be either .css or .less file, because if there are both, then .less will not be found by fallback
     *
     * @param string $area
     * @param string $themePath
     * @param string $locale
     * @param string $module
     * @param string $filePath
     * @dataProvider lessNotConfusedWithCssDataProvider
     */
    public function testLessNotConfusedWithCss($area, $themePath, $locale, $module, $filePath)
    {
        if (false !== strpos($filePath, 'widgets.css')) {
            $filePath .= '';
        }
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        $dirName = dirname($filePath);
        if ('.' == $dirName) {
            $dirName = '';
        } else {
            $dirName .= '/';
        }
        $cssPath = $dirName . $fileName . '.css';
        $lessPath = $dirName . $fileName . '.less';
        $cssFile = $this->getStaticFile($area, $themePath, $locale, $cssPath, $module, true);
        $lessFile = $this->getStaticFile($area, $themePath, $locale, $lessPath, $module, true);
        $this->assertFalse(
            $cssFile && $lessFile,
            "A resource file of only one type must exist. Both found: '$cssFile' and '$lessFile'"
        );
    }

    /**
     * @return array
     */
    public function lessNotConfusedWithCssDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getStaticPreProcessingFiles('*.{less,css}');
    }

    /**
     * Test if references $this->getViewFileUrl() in .phtml-files are correct
     *
     * @param string $phtmlFile
     * @param string $area
     * @param string $themePath
     * @param string $fileId
     * @dataProvider referencesFromPhtmlFilesDataProvider
     */
    public function testReferencesFromPhtmlFiles($phtmlFile, $area, $themePath, $fileId)
    {
        list($module, $filePath) = \Magento\Framework\View\Asset\Repository::extractModule($fileId);
        $this->assertNotEmpty(
            $this->getStaticFile($area, $themePath, 'en_US', $filePath, $module),
            "Unable to locate '{$fileId}' reference from {$phtmlFile}"
        );
    }

    /**
     * @return array
     */
    public function referencesFromPhtmlFilesDataProvider()
    {
        $result = array();
        foreach (\Magento\TestFramework\Utility\Files::init()->getPhtmlFiles(true, false) as $info) {
            list($area, $themePath, , , $file) = $info;
            foreach ($this->collectGetViewFileUrl($file) as $fileId) {
                $result[] = array($file, $area, $themePath, $fileId);
            }
        }
        return $result;
    }

    /**
     * Find invocations of $this->getViewFileUrl() and extract the first argument value
     *
     * @param string $file
     * @return array
     */
    private function collectGetViewFileUrl($file)
    {
        $result = array();
        if (preg_match_all('/\$this->getViewFileUrl\(\'([^\']+?)\'\)/', file_get_contents($file), $matches)) {
            foreach ($matches[1] as $fileId) {
                $result[] = $fileId;
            }
        }
        return $result;
    }

    /**
     * @param string $layoutFile
     * @param string $area
     * @param string $themePath
     * @param string $fileId
     * @dataProvider referencesFromLayoutFilesDataProvider
     */
    public function testReferencesFromLayoutFiles($layoutFile, $area, $themePath, $fileId)
    {
        list($module, $filePath) = \Magento\Framework\View\Asset\Repository::extractModule($fileId);
        $this->assertNotEmpty(
            $this->getStaticFile($area, $themePath, 'en_US', $filePath, $module),
            "Unable to locate '{$fileId}' reference from layout XML in {$layoutFile}"
        );
    }

    /**
     * @return array
     */
    public function referencesFromLayoutFilesDataProvider()
    {
        $result = array();
        $files = \Magento\TestFramework\Utility\Files::init()->getLayoutFiles(array('with_metainfo' => true), false);
        foreach ($files as $metaInfo) {
            list($area, $themePath, , ,$file) = $metaInfo;
            foreach ($this->collectFileIdsFromLayout($file) as $fileId) {
                $result[] = array($file, $area, $themePath, $fileId);
            }
        }
        return $result;
    }

    /**
     * Collect view file declarations in layout XML-files
     *
     * @param string $file
     * @return array
     */
    private function collectFileIdsFromLayout($file)
    {
        $xml = simplexml_load_file($file);
        // Collect "addCss" and "addJs" from theme layout
        $elements = $xml->xpath(
            '//block[@class="Magento\Theme\Block\Html\Head\Css" or @class="Magento\Theme\Block\Html\Head\Script"]' .
            '/arguments/argument[@name="file"]'
        );
        $result = array();
        if ($elements) {
            foreach ($elements as $node) {
                $result[] = (string)$node;
            }
        }
        return $result;
    }
}
