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
     * @param Mage_Core_Model_Theme $theme
     * @param string $file
     * @dataProvider viewFilesFromThemesDataProvider
     */
    public function testViewFilesFromThemes($theme, $file)
    {
        $params = array(
            'area'       => $theme->getArea(),
            'themeModel' => $theme
        );
        $viewFile = Mage::getDesign()->getViewFile($file, $params);
        $this->assertFileExists($viewFile);

        $fileParts = explode(Mage_Core_Model_Design_Package::SCOPE_SEPARATOR, $file);
        if (count($fileParts) > 1) {
            $params['module'] = $fileParts[0];
        }
        if (pathinfo($file, PATHINFO_EXTENSION) == 'css') {
            $errors = array();
            $content = file_get_contents($viewFile);
            preg_match_all(Mage_Core_Model_Design_Package::REGEX_CSS_RELATIVE_URLS, $content, $matches);
            foreach ($matches[1] as $relativePath) {
                $path = $this->_addCssDirectory($relativePath, $file);
                $pathFile = Mage::getDesign()->getViewFile($path, $params);
                if (!is_file($pathFile)) {
                    $errors[] = $relativePath;
                }
            }
            if (!empty($errors)) {
                $this->fail('Can not find file(s): ' . implode(', ', $errors));
            }
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
        foreach ($themes as $theme) {
            $this->_collectGetViewUrlInvokes($theme, $files);
        }

        // Populate data provider in correspondence of themes to view files
        $result = array();
        foreach ($themes as $theme) {
            if (!isset($files[$theme->getArea()][$theme->getId()])) {
                continue;
            }
            foreach (array_unique($files[$theme->getArea()][$theme->getId()]) as $file) {
                $result["{$theme->getArea()}//{$theme->getId()}/{$file}"] =
                    array($theme, $file);
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
        $themePath = str_replace(Mage_Core_Model_Theme::PATH_SEPARATOR, DIRECTORY_SEPARATOR, $theme->getThemePath());
        $searchDir = Mage::getBaseDir('design') . DIRECTORY_SEPARATOR . $theme->getArea()
            . DIRECTORY_SEPARATOR . $themePath;
        $dirLength = strlen($searchDir);
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($searchDir)) as $fileInfo) {
            // Check that file path is valid
            $relativePath = substr($fileInfo->getPath(), $dirLength);
            if (!$this->_validateTemplatePath($relativePath)) {
                continue;
            }

            // Scan file for references to other files
            foreach ($this->_findReferencesToViewFile($fileInfo) as $file) {
                $files[$theme->getArea()][$theme->getId()][] = $file;
            }
        }

        // Collect "addCss" and "addJs" from theme layout
        $layoutUpdate = Mage::getModel('Mage_Core_Model_Layout_Merge',
            array('arguments' => array('area' => $theme->getArea(), 'theme' => $theme->getId()))
        );
        $fileLayoutUpdates = $layoutUpdate->getFileLayoutUpdatesXml();
        $elements = $fileLayoutUpdates->xpath('//action[@method="addCss" or @method="addJs"]/*[1]');
        if ($elements) {
            foreach ($elements as $filenameNode) {
                $viewFile = (string)$filenameNode;
                if ($this->_isFileForDisabledModule($viewFile)) {
                    continue;
                }
                $files[$theme->getArea()][$theme->getId()][] = $viewFile;
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
