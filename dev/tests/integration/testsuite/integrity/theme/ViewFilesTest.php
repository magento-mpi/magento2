<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Theme_ViewFilesTest extends Magento_Test_TestCase_IntegrityAbstract
{
    /**
     * @param string $area
     * @param string $themeId
     * @param string $file
     * @dataProvider viewFilesFromThemesDataProvider
     * @throws PHPUnit_Framework_AssertionFailedError|Exception
     */
    public function testViewFilesFromThemes($area, $themeId, $file)
    {
        $this->_markTestIncompleteDueToBug($area, $file);
        try {
            $params = array('area' => $area, 'themeId' => $themeId);
            $viewFile = Mage::getDesign()->getViewFile($file, $params);
            $this->assertFileExists($viewFile);

            $fileParts = explode(Mage_Core_Model_Design_PackageInterface::SCOPE_SEPARATOR, $file);
            if (count($fileParts) > 1) {
                $params['module'] = $fileParts[0];
            }
            if (pathinfo($file, PATHINFO_EXTENSION) == 'css') {
                $errors = array();
                $content = file_get_contents($viewFile);
                preg_match_all(Mage_Core_Helper_Css::REGEX_CSS_RELATIVE_URLS, $content, $matches);
                foreach ($matches[1] as $relativePath) {
                    $path = $this->_addCssDirectory($relativePath, $file);
                    $pathFile = Mage::getDesign()->getViewFile($path, $params);
                    if (!is_file($pathFile)) {
                        $errors[] = $relativePath;
                    }
                }
                if (!empty($errors)) {
                    $this->fail('Cannot find file(s): ' . implode(', ', $errors));
                }
            }
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            throw $e;
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * This dummy method was introduced to circumvent cyclomatic complexity check
     *
     * @param string $area
     * @param string $file
     */
    protected function _markTestIncompleteDueToBug($area, $file)
    {
        if ($area === 'frontend' && in_array($file, array(
            'css/styles.css', 'js/head.js', 'mui/reset.css', 'js/jquery.dropdowns.js', 'js/tabs.js',
            'js/selectivizr-min.js',
        ))) {
            $this->markTestIncomplete('MAGETWO-9806');
        }
    }

    /**
     * Analyze path to a file in CSS url() directive and add the original CSS-file relative path to it
     *
     * @param string $relativePath
     * @param string $sourceFile
     * @return string
     * @throws Exception if the specified relative path cannot be apparently resolved
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
                    throw new Exception('Invalid file: ' . $file);
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
     * @return array
     */
    public function viewFilesFromThemesDataProvider()
    {
        $themes = $this->_getDesignThemes();

        // Find files, declared in views
        $files = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themes as $theme) {
            if ($theme->getFullPath() == 'frontend/magento2/reference') {
                /** Skip the theme because of MAGETWO-9063 */
                continue;
            }
            $this->_collectGetViewUrlInvokes($theme, $files);
        }

        // Populate data provider in correspondence of themes to view files
        $result = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themes as $theme) {
            if (!isset($files[$theme->getId()])) {
                continue;
            }
            foreach (array_unique($files[$theme->getId()]) as $file) {
                $result["{$theme->getId()}/{$file}"] = array($theme->getArea(), $theme->getId(), $file);
            }
        }
        return array_values($result);
    }

    /**
     * Collect getViewUrl() from theme templates
     *
     * @param Mage_Core_Model_Theme $theme
     * @param array &$files
     */
    protected function _collectGetViewUrlInvokes($theme, &$files)
    {
        $searchDir = $theme->getThemeFilesPath();
        $dirLength = strlen($searchDir);
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($searchDir)) as $fileInfo) {
            // Check that file path is valid
            $relativePath = substr($fileInfo->getPath(), $dirLength);
            if (!$this->_validateTemplatePath($relativePath)) {
                continue;
            }

            // Scan file for references to other files
            foreach ($this->_findReferencesToViewFile($fileInfo) as $file) {
                $files[$theme->getId()][] = $file;
            }
        }

        // Collect "addCss" and "addJs" from theme layout
        /** @var Mage_Core_Model_Layout_Merge $layoutUpdate */
        $layoutUpdate = Mage::getModel('Mage_Core_Model_Layout_Merge',
            array('arguments' => array('area' => $theme->getArea(), 'theme' => $theme))
        );
        $fileLayoutUpdates = $layoutUpdate->getFileLayoutUpdatesXml();
        $elements = $fileLayoutUpdates->xpath('//action[@method="addCss" or @method="addJs"]/*[1]');
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
        $parts = explode(DIRECTORY_SEPARATOR, $relativePath);
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
     * @param SplFileInfo $fileInfo
     * @return array
     */
    protected function _findReferencesToViewFile(SplFileInfo $fileInfo)
    {
        $result = array();
        if (preg_match_all(
            '/\$this->getViewUrl\(\'([^\']+?)\'\)/', file_get_contents($fileInfo->getRealPath()), $matches)
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
        $this->assertFileExists(Mage::getBaseDir('jslib') . DIRECTORY_SEPARATOR . $file);
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
