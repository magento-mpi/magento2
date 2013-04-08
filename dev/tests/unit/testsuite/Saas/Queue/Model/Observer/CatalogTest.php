<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Queue_Model_Observer_CatalogTest extends PHPUnit_Framework_TestCase
{
    /**
     * Instance of indexer observer mock
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerObserverMock;

    /**
     * Instance of cache observer mock
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheObserverMock;

    /**
     * Instance of config observer mock
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configObserverMock;

    /**
     * Instance of catalog observer model
     *
     * @var Saas_Queue_Model_Observer_Catalog
     */
    protected $_model;

    /**
     * Instantiate catalog observer object
     *
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->_indexerObserverMock = $this->getMockBuilder('Saas_Queue_Model_Observer_Indexer')
            ->disableOriginalConstructor()->getMock();
        $this->_cacheObserverMock = $this->getMockBuilder('Saas_Queue_Model_Observer_Cache')
            ->disableOriginalConstructor()->getMock();
        $this->_configObserverMock = $this->getMockBuilder('Saas_Queue_Model_Observer_Config')
            ->disableOriginalConstructor()->getMock();

        $this->_model = new Saas_Queue_Model_Observer_Catalog(
            $this->_indexerObserverMock,
            $this->_cacheObserverMock,
            $this->_configObserverMock
        );
    }

    protected function tearDown()
    {
        unset($this->_indexerObserverMock);
        unset($this->_cacheObserverMock);
        unset($this->_configObserverMock);
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
     * Checks that all sub-processes runs much times as necessary
     *
     * List of sub-processes:
     *  - Saas_Queue_Model_Observer_Indexer::processReindexAll
     *  - Saas_Queue_Model_Observer_Cache::processRefreshAllCache
     *  - Saas_Queue_Model_Observer_Config::processReinitConfig
     */
    public function testProcessRefreshCatalog()
    {
        $observer = new Varien_Event_Observer();

        $this->_indexerObserverMock->expects($this->once())->method('processReindexAll');
        $this->_cacheObserverMock->expects($this->once())->method('processRefreshAllCache');
        $this->_configObserverMock->expects($this->once())->method('processReinitConfig');

        $this->_model->processRefreshCatalog($observer);
    }
}
