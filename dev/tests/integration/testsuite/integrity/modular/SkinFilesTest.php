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
class Integrity_Modular_SkinFilesTest extends Magento_Test_TestCase_IntegrityAbstract
{
    /**
     * @param string $application
     * @param string $file
     * @dataProvider skinFilesFromModulesViewDataProvider
     */
    public function testSkinFilesFromModulesView($application, $file)
    {
        $params = array(
            '_area'    => $application,
            '_package' => 'default',
            '_theme'   => 'default',
            '_skin'    => 'default'
        );
        $this->assertFileExists(Mage::getDesign()->getSkinFile($file, $params));
    }

    /**
     * Collect getSkinUrl() calls from base templates
     *
     * @return array
     */
    public function skinFilesFromModulesViewDataProvider()
    {
        $files = array();
        foreach($this->_getEnabledModules() as $moduleName) {
            $moduleViewDir = Mage::getConfig()->getModuleDir('view', $moduleName);
            if (!is_dir($moduleViewDir)) {
                continue;
            }
            foreach (new DirectoryIterator($moduleViewDir) as $viewAppDir) {
                $area = $viewAppDir->getFilename();
                if (0 === strpos($area, '.') || !$viewAppDir->isDir()) {
                    continue;
                }
                foreach (new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($viewAppDir->getRealPath())) as $fileInfo
                ) {
                    $references = $this->_findReferencesToSkinFile($fileInfo);
                    if (!isset($files[$area])) {
                        $files[$area] = $references;
                    } else {
                        $files[$area] = array_merge($files[$area], $references);
                    }
                    $files[$area] = array_unique($files[$area]);
                }
            }
        }
        $result = array();
        foreach ($files as $area => $references) {
            foreach ($references as $file) {
                $result[] = array($area, $file);
            }
        }
        return $result;
    }

    /**
     * Scan specified file for getSkinUrl() pattern
     *
     * @param SplFileInfo $fileInfo
     * @return array
     */
    protected function _findReferencesToSkinFile(SplFileInfo $fileInfo)
    {
        if (!$fileInfo->isFile() || !preg_match('/\.phtml$/', $fileInfo->getFilename())) {
            return array();
        }

        $result = array();
        $content = file_get_contents($fileInfo->getRealPath());
        if (preg_match_all('/\$this->getSkinUrl\(\'([^\']+?)\'\)/', $content, $matches)) {
            foreach ($matches[1] as $value) {
                if ($this->_isFileForDisabledModule($value)) {
                    continue;
                }
                $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * getSkinUrl() hard-coded in the php-files
     *
     * @param string $application
     * @param string $file
     * @dataProvider skinFilesFromModulesCodeDataProvider
     */
    public function testSkinFilesFromModulesCode($application, $file)
    {
        $this->assertFileExists(Mage::getDesign()->getSkinFile(
            $file,
            array('_area' => $application, '_package' => 'default'))
        );
    }

    /**
     * @return array
     */
    public function skinFilesFromModulesCodeDataProvider()
    {
        // All possible files to test
        $allFiles = array(
            array('adminhtml', 'images/ajax-loader.gif'),
            array('adminhtml', 'images/error_msg_icon.gif'),
            array('adminhtml', 'images/fam_bullet_disk.gif'),
            array('adminhtml', 'images/fam_bullet_success.gif'),
            array('adminhtml', 'images/fam_link.gif'),
            array('adminhtml', 'images/grid-cal.gif'),
            array('adminhtml', 'images/rule_chooser_trigger.gif'),
            array('adminhtml', 'images/rule_component_add.gif'),
            array('adminhtml', 'images/rule_component_apply.gif'),
            array('adminhtml', 'images/rule_component_remove.gif'),
            array('adminhtml', 'Mage_Cms::images/placeholder_thumbnail.jpg'),
            array('adminhtml', 'Mage_Cms::images/wysiwyg_skin_image.png'),
            array('adminhtml', 'Mage_Core::fam_book_open.png'),
            array('adminhtml', 'Mage_Page::favicon.ico'),
            array('frontend',  'Mage_Core::calendar.gif'),
            array('frontend',  'Mage_Core::fam_book_open.png'),
            array('frontend',  'Mage_Page::favicon.ico'),
            array('install',   'Mage_Page::favicon.ico'),
        );

        return $this->_removeDisabledModulesFiles($allFiles);
    }

    /**
     * Scans array of file information and removes files, that belong to disabled modules.
     * Thus we won't test them.
     *
     * @param array $allFiles
     * @return array
     */
    protected function _removeDisabledModulesFiles($allFiles)
    {
        $result = array();
        foreach ($allFiles as $fileInfo) {
            $fileName = $fileInfo[1];
            if ($this->_isFileForDisabledModule($fileName)) {
                continue;
            }
            $result[] = $fileInfo;
        }
        return $result;
    }
}
