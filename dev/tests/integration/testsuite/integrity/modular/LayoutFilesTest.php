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
class Integrity_Modular_LayoutFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider layoutFilesFromModulesDataProvider
     */
    public function testLayoutFilesFromModules($area, $module, $file)
    {
        $this->assertNotEmpty($module);

        $moduleViewDir = Mage::getConfig()->getModuleDir('view', $module);
        $moduleLayoutDir = $moduleViewDir . DIRECTORY_SEPARATOR . $area;

        $this->assertFileExists($moduleViewDir, 'Expected existence of the module view directory.');
        $this->assertFileExists($moduleLayoutDir, 'Expected existence of the module layout directory.');

        $params = array(
            '_area'    => $area,
            '_module'  => $module,
        );
        $layoutFilename = Mage::getDesign()->getFilename($file, $params);

        $this->assertStringStartsWith($moduleLayoutDir, $layoutFilename);
        $this->assertFileExists($layoutFilename, 'Expected existence of the layout file.');
    }

    /**
     * @return array
     */
    public function layoutFilesFromModulesDataProvider()
    {
        $result = array();
        foreach (array('frontend', 'adminhtml') as $area) {
            $updatesRoot = Mage::getConfig()->getNode($area . '/layout/updates');
            foreach ($updatesRoot->children() as $updateNode) {
                $file = (string)$updateNode->file;
                $module = $updateNode->getAttribute('module');
                $result[] = array($area, $module, $file);
            }
        }
        return $result;
    }
}
