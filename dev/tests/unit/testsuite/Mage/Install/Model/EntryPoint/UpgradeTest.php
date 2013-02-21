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

    public function testProcessRequestIndex()
    {
        $cache = $this->getMock('Mage_Core_Model_Cache', array('flush'), array(), '', false);
        $appState = $this->getMock('Mage_Core_Model_App_State', array('setIsDeveloperMode'), array(), '', false);
        $update = $this->getMock('Mage_Core_Model_Db_Updater', array('updateScheme', 'updateData'), array(), '', false);
        $indexer = $this->getMock(
            'Mage_Index_Model_Indexer', array('reindexAll', 'reindexRequired'), array(), '', false
        );
        $indexer->expects($this->at(0))->method('reindexAll');
        $indexer->expects($this->at(1))->method('reindexRequired');
        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager->expects($this->any())->method('get')->will($this->returnValueMap(array(
            array('Mage_Core_Model_Cache', array(), $cache),
            array('Mage_Core_Model_App_State', array(), $appState),
            array('Mage_Core_Model_Db_Updater', array(), $update),
            array('Mage_Index_Model_Indexer', array(), $indexer),
        )));

        $upgrade = new Mage_Install_Model_EntryPoint_Upgrade(__DIR__, array(
                Mage_Install_Model_EntryPoint_Upgrade::REINDEX => Mage_Install_Model_EntryPoint_Upgrade::REINDEX_ALL
            ), $objectManager
        );
        $upgrade->processRequest();

        $upgrade = new Mage_Install_Model_EntryPoint_Upgrade(__DIR__, array(
                Mage_Install_Model_EntryPoint_Upgrade::REINDEX => Mage_Install_Model_EntryPoint_Upgrade::REINDEX_INVALID
            ), $objectManager
        );
        $upgrade->processRequest();
    }
}
