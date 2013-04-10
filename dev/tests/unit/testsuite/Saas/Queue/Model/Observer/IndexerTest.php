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
     * Instantiate saas queue indexer observer
     *
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->_indexerMock = $this->getMockBuilder('Mage_Index_Model_Indexer')
            ->disableOriginalConstructor()->getMock();

        $this->_model = new Saas_Queue_Model_Observer_Indexer($this->_indexerMock);
    }

    protected function tearDown()
    {
        unset($this->_indexerMock);
        unset($this->_model);
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
     * Checks that Mage_Index_Model_Indexer::reindexAll() is called
     */
    public function testProcessReindexAll()
    {
        $observer = new Varien_Event_Observer();
        $this->_indexerMock->expects($this->once())->method('reindexAll');
        $this->_model->processReindexAll($observer);
    }

    /**
     * Test for method processReindexRequired
     *
     * Checks that Mage_Index_Model_Indexer::reindexRequired() is called
     */
    public function testProcessReindexRequired()
    {
        $observer = new Varien_Event_Observer();
        $this->_indexerMock->expects($this->once())->method('reindexRequired');
        $this->_model->processReindexRequired($observer);
    }
}