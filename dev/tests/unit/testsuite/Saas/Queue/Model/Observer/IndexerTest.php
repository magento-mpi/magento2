<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Queue_Model_Observer_IndexerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Instance of indexer mock
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerMock;

    /**
     * Instance of saas queue indexer observer
     *
     * @var Saas_Queue_Model_Observer_Indexer
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_flagMock;

    /**
     * Instantiate saas queue indexer observer
     *
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->_indexerMock = $this->getMockBuilder('Magento_Index_Model_Indexer')
            ->disableOriginalConstructor()->getMock();
        $this->_flagMock = $this->getMock('Saas_Index_Model_Flag',
            array('getState', 'loadSelf', 'setState', 'save'), array(), '', false
        );
        $this->_flagMock->expects($this->once())->method('loadSelf');
        $factoryMock = $this->getMock('Saas_Index_Model_FlagFactory', array('create'), array(), '', false);
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_flagMock));

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Saas_Queue_Model_Observer_Indexer', array(
            'indexer' => $this->_indexerMock,
            'flagFactory' => $factoryMock,
        ));
    }

    /**
     * Test for method useInEmailNotification
     *
     * Checks that method return valid value
     */
    public function testUseInEmailNotification()
    {
        $this->assertFalse($this->_model->useInEmailNotification());
    }

    /**
     * Test for method processRefreshCatalog
     *
     * Checks that Magento_Index_Model_Indexer::reindexAll() is called
     */
    public function testProcessReindexAll()
    {
        $this->_flagMock->expects($this->at(0))->method('setState')->with(Saas_Index_Model_Flag::STATE_PROCESSING);
        $this->_flagMock->expects($this->at(1))->method('save');

        $this->_flagMock->expects($this->at(2))->method('setState')->with(Saas_Index_Model_Flag::STATE_FINISHED);
        $this->_flagMock->expects($this->at(3))->method('save');

        $observer = new Magento_Event_Observer();
        $this->_indexerMock->expects($this->once())->method('reindexAll');
        $this->_model->processReindexAll($observer);
    }

    /**
     * Test for method processReindexRequired
     *
     * Checks that Magento_Index_Model_Indexer::reindexRequired() is called
     */
    public function testProcessReindexRequired()
    {
        $this->_flagMock->expects($this->at(0))->method('setState')->with(Saas_Index_Model_Flag::STATE_PROCESSING);
        $this->_flagMock->expects($this->at(1))->method('save');

        $this->_flagMock->expects($this->at(2))->method('setState')->with(Saas_Index_Model_Flag::STATE_FINISHED);
        $this->_flagMock->expects($this->at(3))->method('save');

        $observer = new Magento_Event_Observer();
        $this->_indexerMock->expects($this->once())->method('reindexRequired');
        $this->_model->processReindexRequired($observer);
    }
}
