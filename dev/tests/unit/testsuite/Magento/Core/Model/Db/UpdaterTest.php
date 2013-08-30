<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Db_UpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Automatic updates must be enabled/disabled according to config flags
     *
     * @dataProvider updateSchemeAndDataConfigDataProvider
     */
    public function testUpdateSchemeAndDataConfig($configXml, $appMode, $expectedUpdates)
    {
        // Configuration
        $configuration = new Magento_Simplexml_Config($configXml);

        $map = array(
            array('global/resources', $configuration->getNode('global/resources')),
            array(
                'global/skip_process_modules_updates_ignore_dev_mode',
                $configuration->getNode('global/skip_process_modules_updates_ignore_dev_mode')
            ),
            array(
                'global/skip_process_modules_updates',
                $configuration->getNode('global/skip_process_modules_updates')
            ),
        );
        $configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $configMock->expects($this->any())
            ->method('getNode')
            ->will($this->returnValueMap($map));

        // Data updates model
        $updateCalls = $expectedUpdates ? 1 : 0;
        $setupModel = $this->getMock('Magento_Core_Model_Resource_Setup', array(), array(), '', false);
        $setupModel->expects($this->exactly($updateCalls))
            ->method('applyUpdates');
        $setupModel->expects($this->exactly($updateCalls))
            ->method('applyDataUpdates');

        $factory = $this->getMock('Magento_Core_Model_Resource_SetupFactory', array(), array(), '', false);
        $factory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($setupModel));

        // Application state
        $appState = $this->getMock('Magento_Core_Model_App_State', array(), array(), '', false);
        $appState->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $appState->expects($this->any())
            ->method('getMode')
            ->will($this->returnValue($appMode));
        $updater = new Mage_Core_Model_Db_Updater($configMock, $factory, $appState);

        // Run and verify
        $updater->updateScheme();
        $updater->updateData();
    }

    public static function updateSchemeAndDataConfigDataProvider()
    {
        $fixturePath = __DIR__ . '/_files/';
        return array(
            'updates (default config)' => array(
                file_get_contents($fixturePath . 'config.xml'),
                Magento_Core_Model_App_State::MODE_DEVELOPER,
                true
            ),
            'no updates when skipped' => array(
                file_get_contents($fixturePath . 'config_skip_updates.xml'),
                Magento_Core_Model_App_State::MODE_DEFAULT,
                false
            ),
            'updates when skipped, if in dev mode' => array(
                file_get_contents($fixturePath . 'config_skip_updates.xml'),
                Magento_Core_Model_App_State::MODE_DEVELOPER,
                true
            ),
            'skipped updates, even in dev mode' => array(
                file_get_contents($fixturePath . 'config_skip_updates_even_in_dev_mode.xml'),
                Magento_Core_Model_App_State::MODE_DEVELOPER,
                false
            )
        );
    }
}
