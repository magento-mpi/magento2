<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class WebsiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Resource\Category\Flat
     */
    protected $flatResourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Code\Plugin\InvocationChain
     */
    protected $pluginMock;

    /**
     * @var Website
     */
    protected $model;

    protected function setUp()
    {
        $this->pluginMock = $this->getMock(
            'Magento\Code\Plugin\InvocationChain', array('proceed'), array(), '', false
        );
        $indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(), '', false, false, true, array('getId', 'getState', '__wakeup')
        );
        $stateMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Flat\State', array('isFlatEnabled'), array(), '', false
        );
        $this->flatResourceMock = $this->getMock(
            'Magento\Catalog\Model\Resource\Category\Flat', array('deleteStores', '__wakeup'), array(), '', false
        );
        $this->model = new Website(
            $indexerMock,
            $stateMock,
            $this->flatResourceMock
        );
    }

    public function testAroundDelete()
    {
        $storeIds = array(1,2,3);
        $this->flatResourceMock->expects($this->once())
            ->method('deleteStores')
            ->with($storeIds);
        $websiteMock = $this->getMock(
            'Magento\Core\Model\Website', array('getStoreIds', '__wakeup'), array(), '', false
        );
        $websiteMock->expects($this->once())
            ->method('getStoreIds')
            ->will($this->returnValue($storeIds));
        $arguments = array($websiteMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundDelete($arguments, $this->pluginMock));
    }

    public function testAroundDeleteWithoutId()
    {
        $this->flatResourceMock->expects($this->never())
            ->method('deleteStores');
        $websiteMock = $this->getMock(
            'Magento\Core\Model\Website', array('getStoreIds', '__wakeup'), array(), '', false
        );
        $websiteMock->expects($this->once())
            ->method('getStoreIds')
            ->will($this->returnValue(array()));
        $arguments = array($websiteMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundDelete($arguments, $this->pluginMock));
    }

    protected function mockPluginProceed($arguments, $returnValue = false)
    {
        $this->pluginMock->expects($this->once())
            ->method('proceed')
            ->with($arguments)
            ->will($this->returnValue($returnValue));
    }
}