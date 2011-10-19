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

/**
 * @group integrity
 */
class Integrity_Theme_SkinFilesTest extends Magento_Test_TestCase_IntegrityAbstract
{
    /**
     * @param string $application
     * @param string $package
     * @param string $theme
     * @param string $skin
     * @param string $file
     * @dataProvider skinFilesFromThemesDataProvider
     */
    public function testSkinFilesFromThemes($application, $package, $theme, $skin, $file)
    {
        $params = array(
            '_area'    => $application,
            '_package' => $package,
            '_theme'   => $theme,
            '_skin'    => $skin
        );
        $skinFile = Mage::getDesign()->getSkinFile($file, $params);
        $this->assertFileExists($skinFile);

        $fileParts = explode(Mage_Core_Model_Design_Package::SCOPE_SEPARATOR, $file);
        if (count($fileParts) > 1) {
            $params['_module'] = $fileParts[0];
        }
        if (pathinfo($file, PATHINFO_EXTENSION) == 'css') {
            $errors = array();
            $content = file_get_contents($skinFile);
            preg_match_all('#url\([\'"]?(?!http://|https://|/|data\:)(.+?)[\'"]?\)#', $content, $matches);
            foreach ($matches[1] as $relativePath) {
                $path = $this->_getNotRelativePath($relativePath, $file);
                $pathFile = Mage::getDesign()->getSkinFile($path, $params);
                if (!file_exists($pathFile)) {
                    $errors[] = $relativePath;
                }
            }
            if (!empty($errors)) {
                $this->fail('Can not find file(s): ' . implode(', ', $errors));
            }
        }
    }

    protected function _getNotRelativePath($path, $sourceFile)
    {
        $file = dirname($sourceFile) . '/' . $path;
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
     * Collect getSkinUrl() and similar calls from themes
     *
     * @return array
     */
    public function skinFilesFromThemesDataProvider()
    {
        $skins = $this->_getDesignSkins();

        // Find files, declared in skins
        $files = array();
        foreach ($skins as $view) {
            list($area, $package, $theme) = explode('/', $view);

            // Collect getSkinUrl() from theme templates
            $searchDir = Mage::getBaseDir('design') . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR . $package
                . DIRECTORY_SEPARATOR . $theme;
            $dirLength = strlen($searchDir);
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($searchDir)) as $fileInfo) {
                if (!$fileInfo->isFile() || !preg_match('/\.phtml$/', $fileInfo->getFilename())) {
                    continue;
                }

                // Check that file path includes only enabled modules
                $relativePath = substr($fileInfo->getPath(), $dirLength);
                if ($this->_isPathForDisabledModule($relativePath)) {
                    continue;
                }

                // Scan file for references to other files
                foreach ($this->_findReferencesToSkinFile($fileInfo) as $file) {
                    $files[$area][$package][$theme][] = $file;
                }
            }

            // Collect "addCss" and "addItem" from theme layout
            $layout = Mage::app()->getLayout()->getUpdate()->getFileLayoutUpdatesXml(
                $area, $package, $theme
            );
            foreach ($layout->xpath('//action[@method="addCss"]/*[1] '
                . '| //action[@method="addItem"][*[1][text()="skin_js" or text()="skin_css"]]/*[2]') as $filenameNode) {
                $skinFile = (string) $filenameNode;
                if ($this->_isFileForDisabledModule($skinFile)) {
                    continue;
                }
                $files[$area][$package][$theme][] = $skinFile;
            }
        }

        // Populate data provider in correspondence of skins to views
        $result = array();
        foreach ($skins as $view) {
            list($area, $package, $theme, $skin) = explode('/', $view);
            foreach (array_unique($files[$area][$package][$theme]) as $file) {
                $result["{$area}/{$package}/{$theme}/{$skin}/{$file}"] =
                    array($area, $package, $theme, $skin, $file);
            }
        }

        return array_values($result);
    }

    /**
     * Checks file path - whether there are mentions of disabled modules
     *
     * @param string $relativePath
     * @return bool
     */
    protected function _isPathForDisabledModule($relativePath)
    {
        $relativePath = trim($relativePath, '/\\');
        $parts = explode(DIRECTORY_SEPARATOR, $relativePath);
        $enabledModules = $this->_getEnabledModules();
        foreach ($parts as $part) {
            if (!preg_match('/^[A-Z][[:alnum:]]*_[A-Z][[:alnum:]]*$/', $part)) {
                continue;
            }
            if (!isset($enabledModules[$part])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Scan specified file for getSkinUrl() pattern
     *
     * @param SplFileInfo $fileInfo
     * @return array
     */
    protected function _findReferencesToSkinFile(SplFileInfo $fileInfo)
    {
        $result = array();
        if (preg_match_all(
            '/\$this->getSkinUrl\(\'([^\']+?)\'\)/', file_get_contents($fileInfo->getRealPath()), $matches)
        ) {
            foreach ($matches[1] as $skinFile) {
                if ($this->_isFileForDisabledModule($skinFile)) {
                    continue;
                }
                $result[] = $skinFile;
            }
        }
        return $result;
    }
}
