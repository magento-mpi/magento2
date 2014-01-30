<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class SystemConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\Indexer
     */
    protected $indexerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Code\Plugin\InvocationChain
     */
    protected $pluginMock;

    /**
     * @var SystemConfig
     */
    protected $model;

    protected function setUp()
    {
        $this->pluginMock = $this->getMock(
            'Magento\Code\Plugin\InvocationChain', array('proceed'), array(), '', false
        );

        $this->indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer', array('getId', 'getState'), array(), '', false
        );
        $config = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Flat\Config', array('isFlatEnabled'), array(), '', false
        );
        $config->expects($this->once())
            ->method('isFlatEnabled')
            ->will($this->returnValue(false));
        $this->model = new SystemConfig(
            $this->indexerMock,
            $config
        );
    }

    public function testAroundSave()
    {
        $this->mockIndexerMethods();
        $configMock = $this->getMock(
            'Magento\Core\Model\Config\Value', array('getPath', 'getValue', '__wakeup'), array(), '', false
        );
        $configMock->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('catalog/frontend/flat_catalog_category'));
        $configMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(true));

        $arguments = array($configMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundSave($arguments, $this->pluginMock));
    }

    public function testAroundSaveTurnOff()
    {
        $this->mockIndexerMethodsNever();
        $configMock = $this->getMock(
            'Magento\Core\Model\Config\Value', array('getPath', 'getValue', '__wakeup'), array(), '', false
        );
        $configMock->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('catalog/frontend/flat_catalog_category'));
        $configMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(false));

        $arguments = array($configMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundSave($arguments, $this->pluginMock));
    }

    protected function mockPluginProceed($arguments, $returnValue = false)
    {
        $this->pluginMock->expects($this->once())
            ->method('proceed')
            ->with($arguments)
            ->will($this->returnValue($returnValue));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\Indexer\State
     */
    protected function getStateMock()
    {
        $stateMock = $this->getMock(
            'Magento\Indexer\Model\Indexer\State', array('setStatus', 'save', '__wakeup'), array(), '', false
        );
        $stateMock->expects($this->once())
            ->method('setStatus')
            ->with('invalid')
            ->will($this->returnSelf());
        $stateMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());

        return $stateMock;
    }

    protected function mockIndexerMethods()
    {
        $this->indexerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($this->getStateMock()));
    }

    protected function mockIndexerMethodsNever()
    {
        $this->indexerMock->expects($this->never())
            ->method('getId');
        $this->indexerMock->expects($this->never())
            ->method('getState');
    }
}
