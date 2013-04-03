<?php
/**
 * Test which checks whether all disabled configuration options exist in system
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Saas_Saas_DisabledConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testDisabledConfigurationList()
    {
        $objectManager = Mage::getObjectManager();

        // disable config caching to not pollute it
        /** @var $cacheTypes Mage_Core_Model_Cache_Types */
        $cacheTypes = $objectManager->get('Mage_Core_Model_Cache_Types');
        $cacheTypes->setEnabled(Mage_Core_Model_Cache_Type_Config::TYPE_IDENTIFIER, false);

        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = $objectManager->get('Mage_Core_Model_Dir');
        $modulesDir = $dirs->getDir(Mage_Core_Model_Dir::MODULES);

        $fileList = glob($modulesDir . '/*/*/etc/adminhtml/system.xml');

        $configMock = $this->getMock(
            'Mage_Core_Model_Config_Modules_Reader', array('getModuleConfigurationFiles', 'getModuleDir'),
            array(), '', false
        );
        $configMock->expects($this->any())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue($fileList));
        $configMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Mage_Backend')
            ->will($this->returnValue($modulesDir . '/Mage/Backend/etc'));
        $reader = $objectManager->create(
            'Mage_Backend_Model_Config_Structure_Reader',
            array('moduleReader' => $configMock, 'runtimeValidation' => true)
        );
        $data = $reader->getData();

        $disabledOptions = include($modulesDir . '/Saas/Saas/Model/DisabledConfiguration/disabled_configuration.php');

        foreach ($disabledOptions as $path) {
            $this->_testInChildren($path, $path, $data['sections']);
        }
    }


    /**
     * Recursively assert that
     *
     * @param string $currentPath
     * @param string $wholePath
     * @param array $entries
     */
    protected function _testInChildren($currentPath, $wholePath, array $entries)
    {
        $chunks = explode('/', $currentPath);
        $this->assertArrayHasKey(
            $chunks[0],
            $entries,
            'Path \'' . $wholePath . '\' does not exist in system configuration'
        );
        if (isset($chunks[1])) {
            if (!isset($entries[$chunks[0]]['children'])) {
                $this->fail('Path \'' . $wholePath . '\' does not exist in system configuration');
            }
            $this->_testInChildren(
                substr($currentPath, strlen($chunks[0]) + 1),
                $wholePath,
                $entries[$chunks[0]]['children']
            );
        }
    }

}
