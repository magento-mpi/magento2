<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Install_Model_EntryPoint_UpgradeTest extends PHPUnit_Framework_TestCase
{
    public function testProcessRequest()
    {
        $cache = $this->getMock('Mage_Core_Model_Cache', array('flush'), array(), '', false);
        $appState = $this->getMock('Mage_Core_Model_App_State', array('setIsDeveloperMode'), array(), '', false);
        $update = $this->getMock('Mage_Core_Model_Db_Updater', array('updateScheme', 'updateData'), array(), '', false);
        $update->expects($this->once())->method('updateScheme');
        $update->expects($this->once())->method('updateData');
        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager->expects($this->any())->method('get')->will($this->returnValueMap(array(
            array('Mage_Core_Model_Cache', array(), $cache),
            array('Mage_Core_Model_App_State', array(), $appState),
            array('Mage_Core_Model_Db_Updater', array(), $update),
        )));
        $upgrade = new Mage_Install_Model_EntryPoint_Upgrade(__DIR__, array(), $objectManager);
        $upgrade->processRequest();
    }
}
