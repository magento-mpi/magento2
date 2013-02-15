<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Db_UpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Automatic updates must be enabled/disabled according to config flags
     *
     * @dataProvider updateSchemeAndDataConfigDataProvider
     */
    public function testUpdateSchemeAndDataConfig($configXml, $isDevMode, $expectedUpdates)
    {
        // Configuration
        $configuration = new Varien_Simplexml_Config($configXml);
        $storage = $this->getMock('Mage_Core_Model_Config_Storage', array(), array(), '', false);
        $storage->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration));
        $modulesConfig = new Mage_Core_Model_Config_Modules($storage);

        // Data updates model
        $actualSqlUpdates = false;
        $setSqlUpdates = function () use (&$actualSqlUpdates) {
            $actualSqlUpdates = true;
        };
        $actualDataUpdates = false;
        $setDataUpdates = function () use (&$actualDataUpdates) {
            $actualDataUpdates = true;
        };
        $setupModel = $this->getMock('Mage_Core_Model_Resource_Setup', array(), array(), '', false);
        $setupModel->expects($this->any())
            ->method('applyUpdates')
            ->will($this->returnCallback($setSqlUpdates));
        $setupModel->expects($this->any())
            ->method('applyDataUpdates')
            ->will($this->returnCallback($setDataUpdates));

        $factory = $this->getMock('Mage_Core_Model_Resource_SetupFactory', array(), array(), '', false);
        $factory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($setupModel));

        // Application state
        $appState = $this->getMock('Mage_Core_Model_App_State', array(), array(), '', false);
        $appState->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $appState->expects($this->any())
            ->method('isDeveloperMode')
            ->will($this->returnValue($isDevMode));
        $updater = new Mage_Core_Model_Db_Updater($modulesConfig, $factory, $appState);

        // Run and verify
        $updater->updateScheme();
        $this->assertEquals($expectedUpdates, $actualSqlUpdates);

        $updater->updateData();
        $this->assertEquals($expectedUpdates, $actualDataUpdates);
    }

    public static function updateSchemeAndDataConfigDataProvider()
    {
        $fixturePath = __DIR__ . '/_files/';
        return array(
            'updates (default config)' => array(
                file_get_contents($fixturePath . 'config.xml'),
                false,
                true
            ),
            'no updates when skipped' => array(
                file_get_contents($fixturePath . 'config_skip_updates.xml'),
                false,
                false
            ),
            'updates when skipped, if in dev mode' => array(
                file_get_contents($fixturePath . 'config_skip_updates.xml'),
                true,
                true
            ),
            'skipped updates, even in dev mode' => array(
                file_get_contents($fixturePath . 'config_skip_updates_even_in_dev_mode.xml'),
                true,
                false
            )
        );
    }
}
