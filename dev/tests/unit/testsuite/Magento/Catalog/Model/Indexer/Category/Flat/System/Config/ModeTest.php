<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Flat\System\Config;

class ModeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\System\Config\Mode
     */
    protected $model;

    /**
     * @var \Magento\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Indexer\Model\Indexer\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerStateMock;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $flatIndexerMock;

    protected function setUp()
    {
        $this->configMock = $this->getMock(
            'Magento\App\ConfigInterface',
            array(), array(), '', false
        );
        $this->indexerStateMock = $this->getMock(
            'Magento\Indexer\Model\Indexer\State',
            array('loadByIndexer', 'setStatus', 'save', '__wakeup'), array(), '', false
        );
        $this->flatIndexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(), '', false, false, true, array('load', 'setScheduled', '__wakeup')
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\Catalog\Model\Indexer\Category\Flat\System\Config\Mode',
            [
                'config' => $this->configMock,
                'indexerState' => $this->indexerStateMock,
                'flatIndexer' => $this->flatIndexerMock,
            ]
        );
    }

    public function dataProviderProcessValueEqual()
    {
        return [
            ['0', '0'],
            ['', '0'],
            ['0', ''],
            ['1', '1'],
        ];
    }

    /**
     * @param string $oldValue
     * @param string $value
     * @dataProvider dataProviderProcessValueEqual
     */
    public function testProcessValueEqual($oldValue, $value)
    {
        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with(null, 'default')
            ->will($this->returnValue($oldValue));

        $this->model->setValue($value);

        $this->indexerStateMock->expects($this->never())
            ->method('loadByIndexer');
        $this->indexerStateMock->expects($this->never())
            ->method('setStatus');
        $this->indexerStateMock->expects($this->never())
            ->method('save');

        $this->flatIndexerMock->expects($this->never())
            ->method('load');
        $this->flatIndexerMock->expects($this->never())
            ->method('setScheduled');

        $this->model->processValue();
    }

    public function dataProviderProcessValueOn()
    {
        return [
            ['0', '1'],
            ['', '1'],
        ];
    }

    /**
     * @param string $oldValue
     * @param string $value
     * @dataProvider dataProviderProcessValueOn
     */
    public function testProcessValueOn($oldValue, $value)
    {
        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with(null, 'default')
            ->will($this->returnValue($oldValue));

        $this->model->setValue($value);

        $this->indexerStateMock->expects($this->once())
            ->method('loadByIndexer')
            ->with('catalog_category_flat')
            ->will($this->returnSelf());
        $this->indexerStateMock->expects($this->once())
            ->method('setStatus')
            ->with('invalid')
            ->will($this->returnSelf());
        $this->indexerStateMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());

        $this->flatIndexerMock->expects($this->never())
            ->method('load');
        $this->flatIndexerMock->expects($this->never())
            ->method('setScheduled');

        $this->model->processValue();
    }

    public function dataProviderProcessValueOff()
    {
        return [
            ['1', '0'],
            ['1', ''],
        ];
    }

    /**
     * @param string $oldValue
     * @param string $value
     * @dataProvider dataProviderProcessValueOff
     */
    public function testProcessValueOff($oldValue, $value)
    {
        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with(null, 'default')
            ->will($this->returnValue($oldValue));

        $this->model->setValue($value);

        $this->indexerStateMock->expects($this->never())
            ->method('loadByIndexer');
        $this->indexerStateMock->expects($this->never())
            ->method('setStatus');
        $this->indexerStateMock->expects($this->never())
            ->method('save');

        $this->flatIndexerMock->expects($this->once())
            ->method('load')
            ->with('catalog_category_flat')
            ->will($this->returnSelf());
        $this->flatIndexerMock->expects($this->once())
            ->method('setScheduled')
            ->with(false);

        $this->model->processValue();
    }

}
