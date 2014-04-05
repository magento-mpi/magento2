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
     * @var \Magento\View\Design\Theme\FlyweightFactory
     */
    protected $themeRepo;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback\ViewFile
     */
    protected $viewFilesFallback;

    /**
     * @var \Magento\View\Asset\Repository
     */
    protected $assetRepo;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectmanager();
        $this->themeRepo = $this->objectManager->get('Magento\View\Design\Theme\FlyweightFactory');
        $this->viewFilesFallback = $this->objectManager->get('Magento\View\Design\FileResolution\Fallback\ViewFile');
        $this->assetRepo = $this->objectManager->get('Magento\View\Asset\Repository');
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
            array($this, 'assertCssLessFiles'),
            $this->viewFilesFromThemesDataProvider($this->_getDesignThemes())
        );
    }

    /**
     * A callback for asserting that there can be one and only one either CSS or LESS file must exist by the same name
     *
     * @param string $fileId
     * @param string $area
     * @param string $themeId
     */
    public function assertCssLessFiles($fileId, $area, $themeId)
    {
        if (substr($fileId, -4) == '.css') {
            $cssSourceFile = $this->resolveFileUsingFallback($fileId, $area, $themeId);
            $cssSourceFileExists = $cssSourceFile && file_exists($cssSourceFile);

            $lessFile = substr($fileId, 0, -4) . '.less';
            $lessSourceFile = $this->resolveFileUsingFallback($lessFile, $area, $themeId);
            $lessSourceFileExists = $lessSourceFile && file_exists($lessSourceFile);

            $this->assertTrue(
                $cssSourceFileExists || $lessSourceFileExists,
                "At least one resource file (css or less) must exist for resource '$fileId'"
            );
            $this->assertFalse(
                $cssSourceFileExists && $lessSourceFileExists,
                "Only one resource file must exist. Both found: '$cssSourceFile' and '$lessSourceFile'"
            );
        }
    }

    /**
     * Resolve a file by specified parameters using fallback and asset repository services
     *
     * @param string $fileId
     * @param string $area
     * @param string $themeId
     * @return bool|string
     */
    private function resolveFileUsingFallback($fileId, $area, $themeId)
    {
        $params = array('area' => $area, 'themeId' => $themeId);
        list($params['module'], $fileId) = \Magento\View\Asset\File::extractModule($fileId);
        $this->assetRepo->updateDesignParams($params);
        $themeModel = $this->themeRepo->create($themeId, $area);
        return $this->viewFilesFallback->getViewFile($area, $themeModel, $params['locale'], $fileId, $params['module']);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testViewFilesFromThemes()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $file
             * @param string $area
             * @param string $themeId
             */
            function ($file, $area, $themeId) {
                $params = array('area' => $area, 'themeId' => $themeId);
                list($params['module'], $file) = \Magento\View\Asset\File::extractModule($file);
                $this->assetRepo->updateDesignParams($params);
                $originalViewFile = $this->viewFilesFallback->getViewFile(
                    $params['area'], $params['themeModel'], $params['locale'], $file, $params['module']
                );
                $this->assertNotEmpty($originalViewFile);
                $this->assertFileExists($originalViewFile);
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), array('css', 'less'))) {
                    $content = file_get_contents($originalViewFile);
                    preg_match_all(\Magento\View\Url\CssResolver::REGEX_CSS_RELATIVE_URLS, $content, $matches);
                    $absentFiles = array();
                    foreach ($matches[1] as $relatedSource) {
                        $relatedParams = $params;
                        $originalRelatedSource = $relatedSource;
                        $relatedSource = $this->_addCssDirectory($relatedSource, $file);
                        list($module, $relatedSource) =
                            \Magento\View\Asset\File::extractModule($relatedSource);
                        if (!empty($module)) {
                            $relatedParams['module'] = $module;
                        }
                        $relatedViewFile = $this->viewFilesFallback->getViewFile(
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
     * Collect usages of view files in themes
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
