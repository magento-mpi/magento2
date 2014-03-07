<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Theme;

class ViewFilesTest extends \Magento\TestFramework\TestCase\AbstractIntegrity
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectmanager();
        $this->viewService = $this->objectManager->get('Magento\View\Service');
        $this->objectManager->configure(array(
            'preferences' => array('Magento\Core\Model\Theme' => 'Magento\Core\Model\Theme\Data')
        ));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testViewLessFilesPreProcessing()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $file
             * @param string $area
             */
            function ($file, $area, $themeId) {
                if (substr($file, -4) == '.css') {
                    $lessFile = substr($file, 0, -4) . '.less';
                    $params = array('area' => $area, 'themeId' => $themeId);

                    $cssAsset = $this->viewService->createAsset($file, $params);
                    $cssSourceFile = $this->viewService->getSourceFile($cssAsset);
                    $cssSourceFileExists = $cssSourceFile && file_exists($cssSourceFile);

                    $lessAsset = $this->viewService->createAsset($lessFile, $params);
                    $lessSourceFile = $this->viewService->getSourceFile($lessAsset);
                    $lessSourceFileExists = $lessSourceFile && file_exists($lessSourceFile);

                    $this->assertTrue(
                        $cssSourceFileExists || $lessSourceFileExists,
                        "At least one resource file (css or less) must exist for resource '$file'"
                    );
                    $this->assertFalse(
                        $cssSourceFileExists && $lessSourceFileExists,
                        "Only one resource file must exist. Both found: '$cssSourceFile' and '$lessSourceFile'"
                    );

                }
            },
            $this->viewFilesFromThemesDataProvider($this->_getDesignThemes())
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testViewFilesFromThemes()
    {
        /** @var \Magento\View\Design\FileResolution\Strategy\Fallback $fallback */
        $fallback = $this->objectManager->get('Magento\View\Design\FileResolution\Strategy\Fallback');

        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $file
             * @param string $area
             * @param string $themeId
             */
            function ($file, $area, $themeId) use ($fallback) {
                $params = array('area' => $area, 'themeId' => $themeId);
                list($params['module'], $file) = \Magento\View\Asset\FileId::extractModule($file);
                $this->viewService->updateDesignParams($params);
                $originalViewFile = $fallback->getViewFile(
                    $params['area'], $params['themeModel'], $params['locale'], $file, $params['module']
                );
                $this->assertNotEmpty($originalViewFile);
                $this->assertFileExists($originalViewFile);
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), array('css', 'less'))) {
                    $content = file_get_contents($originalViewFile);
                    preg_match_all(\Magento\View\Url\CssResolver::REGEX_CSS_RELATIVE_URLS, $content, $matches);
                    $absentFiles = array();
                    if (!empty($matches[1])) {
                        echo $originalViewFile . "\n";
                    }
                    foreach ($matches[1] as $relatedSource) {
                        $relatedParams = $params;
                        $originalRelatedSource = $relatedSource;
                        $relatedSource = $this->_addCssDirectory($relatedSource, $file);
                        list($module, $relatedSource) =
                            \Magento\View\Asset\FileId::extractModule($relatedSource);
                        if (!empty($module)) {
                            $relatedParams['module'] = $module;
                        }
                        $relatedViewFile = $fallback->getViewFile(
                            $relatedParams['area'],
                            $relatedParams['themeModel'],
                            $relatedParams['locale'],
                            $relatedSource,
                            $relatedParams['module']
                        );
                        if (!$relatedViewFile || !is_file($relatedViewFile)) {
                            $absentFiles[] = $originalRelatedSource;
                        }
                    }
                    $this->assertEmpty($absentFiles, 'Cannot find resource(s): ' . implode(', ', $absentFiles));
                }
            },
            $this->viewFilesFromThemesDataProvider($this->_getDesignThemes())
        );
    }

    /**
     * Analyze path to a file in CSS url() directive and add the original CSS-file relative path to it
     *
     * @param string $relativePath
     * @param string $sourceFile
     * @return string
     * @throws \Exception if the specified relative path cannot be apparently resolved
     */
    protected function _addCssDirectory($relativePath, $sourceFile)
    {
        if (strpos($relativePath, '::') > 0) {
            return $relativePath;
        }
        $file = dirname($sourceFile) . '/' . $relativePath;
        $parts = explode('/', $file);
        $result = array();
        foreach ($parts as $part) {
            if ('..' === $part) {
                if (null === array_pop($result)) {
                    throw new \Exception('Invalid file: ' . $file);
                }
            } elseif ('.' !== $part) {
                $result[] = $part;
            }

        }
        return implode('/', $result);
    }

    /**
     * Collect getViewUrl() and similar calls from themes
     *
     * @param \Magento\Core\Model\Theme[] $themes
     * @return array
     */
    public function viewFilesFromThemesDataProvider($themes)
    {
        // Find files, declared in views
        $files = array();
        foreach ($themes as $theme) {
            $this->_collectViewUrlInvokes($theme, $files);
            $this->_collectViewLayoutDeclarations($theme, $files);
        }

        // Populate data provider in correspondence of themes to view files
        $result = array();
        foreach ($themes as $theme) {
            if (!isset($files[$theme->getId()])) {
                continue;
            }
            foreach (array_unique($files[$theme->getId()]) as $file) {
                $result["{$theme->getFullPath()} - {$file}"] = array(
                    'file'  => $file,
                    'area'  => $theme->getArea(),
                    'theme' => $theme->getId()
                );
            }
        }
        return $result;
    }

    /**
     * Collect getViewFileUrl() from theme templates
     *
     * @param \Magento\Core\Model\Theme $theme
     * @param array &$files
     */
    protected function _collectViewUrlInvokes($theme, &$files)
    {
        $searchDir = $theme->getCustomization()->getThemeFilesPath();
        if (empty($searchDir)) {
            return;
        }
        $dirLength = strlen($searchDir);
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($searchDir)) as $fileInfo) {
            // Check that file path is valid
            $relativePath = substr($fileInfo->getPath(), $dirLength);
            if ($this->_validateTemplatePath($relativePath)) {
                // Scan file for references to other files
                foreach ($this->_findReferencesToViewFile($fileInfo) as $file) {
                    $files[$theme->getId()][] = $file;
                }
            }
        }
    }

    /**
     * Collect view files declarations into layout
     *
     * @param \Magento\Core\Model\Theme $theme
     * @param array &$files
     */
    protected function _collectViewLayoutDeclarations($theme, &$files)
    {
        // Collect "addCss" and "addJs" from theme layout
        /** @var \Magento\View\Layout\ProcessorInterface $layoutUpdate */
        $layoutUpdate = $this->objectManager->create('Magento\View\Layout\ProcessorInterface',
            array('theme' => $theme));
        $fileLayoutUpdates = $layoutUpdate->getFileLayoutUpdatesXml();
        $elements = $fileLayoutUpdates->xpath(
            '//block[@class="Magento\Theme\Block\Html\Head\Css" or @class="Magento\Theme\Block\Html\Head\Script"]'
            . '/arguments/argument[@name="file"]'
        );
        if ($elements) {
            foreach ($elements as $filenameNode) {
                $viewFile = (string)$filenameNode;
                if ($this->_isFileForDisabledModule($viewFile)) {
                    continue;
                }
                $files[$theme->getId()][] = $viewFile;
            }
        }
    }

    /**
     * Checks file path - whether there are mentions of disabled modules
     *
     * @param string $relativePath
     * @return bool
     */
    protected function _validateTemplatePath($relativePath)
    {
        if (!preg_match('/\.phtml$/', $relativePath)) {
            return false;
        }
        $relativePath = trim($relativePath, '/\\');
        $parts = explode('/', $relativePath);
        $enabledModules = $this->_getEnabledModules();
        foreach ($parts as $part) {
            if (!preg_match('/^[A-Z][[:alnum:]]*_[A-Z][[:alnum:]]*$/', $part)) {
                continue;
            }
            if (!isset($enabledModules[$part])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scan specified file for getViewUrl() pattern
     *
     * @param \SplFileInfo $fileInfo
     * @return array
     */
    protected function _findReferencesToViewFile(\SplFileInfo $fileInfo)
    {
        $result = array();
        if (preg_match_all(
            '/\$this->getViewFileUrl\(\'([^\']+?)\'\)/', file_get_contents($fileInfo->getRealPath()), $matches)
        ) {
            foreach ($matches[1] as $viewFile) {
                if ($this->_isFileForDisabledModule($viewFile)) {
                    continue;
                }
                $result[] = $viewFile;
            }
        }
        return $result;
    }

    /**
     * @param string $file
     * @dataProvider staticLibsDataProvider
     */
    public function testStaticLibs($file)
    {
        $this->markTestIncomplete('Should be fixed when static when we have static folder jslib implemented');
        $this->assertFileExists(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Filesystem')->getPath('jslib')
                . '/' . $file
        );
    }

    /**
     * @return array
     */
    public function staticLibsDataProvider()
    {
        return array(
            array('media/editor.swf'),
            array('media/flex.swf'), // looks like this one is not used anywhere
            array('media/uploader.swf'),
            array('media/uploaderSingle.swf'),
        );
    }
}
