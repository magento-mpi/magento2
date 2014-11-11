<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule;

class AbstractProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractProcessor
     */
    protected $_processor;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Status\Container|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_statusContainer;

    /**
     * @var \Magento\Indexer\Model\Indexer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexer;

    /**
     * @var \Magento\Indexer\Model\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    /**
     * @var \Magento\Indexer\Model\Indexer\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_state;

    public function setUp()
    {
        $this->_indexer = $this->getMock(
            '\Magento\Indexer\Model\Indexer',
            ['getState', 'load'],
            [],
            '',
            false
        );

        $this->_statusContainer = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Status\Container',
            ['setFullReindexPassed', 'isFullReindexPassed'],
            [],
            '',
            false
        );

        $this->indexerRegistryMock = $this->getMock('Magento\Indexer\Model\IndexerRegistry', ['get'], [], '', false);

        $this->_processor = $this->getMockForAbstractClass(
            '\Magento\TargetRule\Model\Indexer\TargetRule\AbstractProcessor',
            [$this->indexerRegistryMock, $this->_statusContainer]
        );
    }

    public function testIsFullReindexPassed()
    {
        $this->_statusContainer->expects($this->once())
            ->method('isFullReindexPassed')
            ->with($this->_processor->getIndexerId());
        $this->_processor->isFullReindexPassed();
    }

    public function testSetFullReindexPassed()
    {
        $this->_state = $this->getMock(
            '\Magento\Indexer\Model\Indexer\State',
            ['setStatus', 'save', '__sleep', '__wakeup'],
            [],
            '',
            false
        );

        $this->_state->expects($this->once())
            ->method('setStatus')
            ->with(\Magento\Indexer\Model\Indexer\State::STATUS_VALID)
            ->will($this->returnSelf());

        $this->_state->expects($this->once())
            ->method('save');

        $this->_statusContainer->expects($this->once())
            ->method('setFullReindexPassed')
            ->with($this->_processor->getIndexerId());

        $this->_indexer->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($this->_state));
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with('')
            ->will($this->returnValue($this->_indexer));

        $this->_processor->setFullReindexPassed();
    }
}
