<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Install_Model_EntryPoint_UpgradeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexer;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $cacheFrontend = $this->getMockForAbstractClass('Magento_Cache_FrontendInterface');
        $cacheFrontend->expects($this->once())->method('clean')->with('all', array());
        $cacheFrontendPool = $this->getMock(
            'Mage_Core_Model_Cache_Frontend_Pool', array('valid', 'current'), array(
                $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Cache_Frontend_Factory', array(), array(), '', false),
            )
        );
        $cacheFrontendPool->expects($this->at(0))->method('valid')->will($this->returnValue(true));
        $cacheFrontendPool->expects($this->once())->method('current')->will($this->returnValue($cacheFrontend));

        $appState = $this->getMock('Mage_Core_Model_App_State', array('setIsDeveloperMode'), array(), '', false);

        $update = $this->getMock('Mage_Core_Model_Db_Updater', array('updateScheme', 'updateData'), array(), '', false);
        $update->expects($this->once())->method('updateScheme');
        $update->expects($this->once())->method('updateData');

        $this->_indexer = $this->getMock(
            'Mage_Index_Model_Indexer', array('reindexAll', 'reindexRequired'), array(), '', false
        );

        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_objectManager->expects($this->any())->method('get')->will($this->returnValueMap(array(
            array('Mage_Core_Model_Cache_Frontend_Pool', array(), $cacheFrontendPool),
            array('Mage_Core_Model_App_State', array(), $appState),
            array('Mage_Core_Model_Db_Updater', array(), $update),
            array('Mage_Index_Model_Indexer', array(), $this->_indexer),
        )));
    }

    /**
     * @param array $inputParams
     * @param int $reindexAllCount
     * @param int $reindexReqCount
     * @dataProvider processRequestDataProvider
     */
    public function testProcessRequest(array $inputParams, $reindexAllCount, $reindexReqCount)
    {
        $this->_indexer->expects($this->exactly($reindexAllCount))->method('reindexAll');
        $this->_indexer->expects($this->exactly($reindexReqCount))->method('reindexRequired');
        $upgrade = new Mage_Install_Model_EntryPoint_Upgrade(__DIR__, $inputParams, $this->_objectManager);
        $upgrade->processRequest();
    }

    public function processRequestDataProvider()
    {
        $reindexParam = Mage_Install_Model_EntryPoint_Upgrade::REINDEX;
        return array(
            'no reindex' => array(
                array(), 0, 0
            ),
            'reindex all' => array(
                array($reindexParam => Mage_Install_Model_EntryPoint_Upgrade::REINDEX_ALL), 1, 0
            ),
            'reindex required' => array(
                array($reindexParam => Mage_Install_Model_EntryPoint_Upgrade::REINDEX_INVALID), 0, 1
            ),
        );
    }
}