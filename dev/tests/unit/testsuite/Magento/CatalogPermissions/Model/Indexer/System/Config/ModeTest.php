<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\System\Config;

class ModeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Indexer\System\Config\Mode
     */
    protected $model;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Indexer\Model\Indexer\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerStateMock;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerMock;

    protected function setUp()
    {
        $this->configMock = $this->getMock('Magento\App\Config\ScopeConfigInterface');
        $this->indexerStateMock = $this->getMock(
            'Magento\Indexer\Model\Indexer\State',
            array('loadByIndexer', 'setStatus', 'save', '__wakeup'),
            array(),
            '',
            false
        );
        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(),
            '',
            false,
            false,
            true,
            array('load', 'setScheduled', '__wakeup')
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\CatalogPermissions\Model\Indexer\System\Config\Mode',
            array(
                'config' => $this->configMock,
                'indexer' => $this->indexerMock,
                'indexerState' => $this->indexerStateMock
            )
        );
    }

    public function dataProviderProcessValueEqual()
    {
        return array(array('0', '0'), array('', '0'), array('0', ''), array('1', '1'));
    }

    /**
     * @param string $oldValue
     * @param string $value
     * @dataProvider dataProviderProcessValueEqual
     */
    public function testProcessValueEqual($oldValue, $value)
    {
        $this->configMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            null,
            'default'
        )->will(
            $this->returnValue($oldValue)
        );

        $this->model->setValue($value);

        $this->indexerStateMock->expects($this->never())->method('loadByIndexer');
        $this->indexerStateMock->expects($this->never())->method('setStatus');
        $this->indexerStateMock->expects($this->never())->method('save');

        $this->indexerMock->expects($this->never())->method('load');
        $this->indexerMock->expects($this->never())->method('setScheduled');

        $this->model->processValue();
    }

    public function dataProviderProcessValueOn()
    {
        return array(array('0', '1'), array('', '1'));
    }

    /**
     * @param string $oldValue
     * @param string $value
     * @dataProvider dataProviderProcessValueOn
     */
    public function testProcessValueOn($oldValue, $value)
    {
        $this->configMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            null,
            'default'
        )->will(
            $this->returnValue($oldValue)
        );

        $this->model->setValue($value);

        $map = array(
            array(
                \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID,
                \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID
            ),
            array($this->returnSelf(), $this->returnSelf())
        );
        $this->indexerStateMock->expects($this->once())->method('loadByIndexer')->will($this->returnValueMap($map));
        $this->indexerStateMock->expects(
            $this->once()
        )->method(
            'setStatus'
        )->with(
            'invalid'
        )->will(
            $this->returnSelf()
        );
        $this->indexerStateMock->expects($this->once())->method('save')->will($this->returnSelf());

        $this->indexerMock->expects($this->never())->method('load');
        $this->indexerMock->expects($this->never())->method('setScheduled');

        $this->model->processValue();
    }

    public function dataProviderProcessValueOff()
    {
        return array(array('1', '0'), array('1', ''));
    }

    /**
     * @param string $oldValue
     * @param string $value
     * @dataProvider dataProviderProcessValueOff
     */
    public function testProcessValueOff($oldValue, $value)
    {
        $this->configMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            null,
            'default'
        )->will(
            $this->returnValue($oldValue)
        );

        $this->model->setValue($value);

        $this->indexerStateMock->expects($this->never())->method('loadByIndexer');
        $this->indexerStateMock->expects($this->never())->method('setStatus');
        $this->indexerStateMock->expects($this->never())->method('save');

        $map = array(
            array(
                \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID,
                \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID
            ),
            array($this->returnSelf(), $this->returnSelf())
        );
        $this->indexerMock->expects($this->exactly(2))->method('load')->will($this->returnValueMap($map));
        $this->indexerMock->expects($this->exactly(2))->method('setScheduled')->with(false);

        $this->model->processValue();
    }
}
