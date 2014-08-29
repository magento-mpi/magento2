<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend;

use Magento\Framework\Model\Exception;

/**
 * Test \Magento\Backend\Model\Config\Backend\Domain
 */
class DomainTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Model\Resource\AbstractResource | \PHPUnit_Framework_MockObject_MockObject */
    protected $resourceMock;

    /** @var \Magento\Backend\Model\Config\Backend\Domain */
    protected $domain;

    protected function setUp()
    {
        $eventDispatcherMock = $this->getMock('Magento\Framework\Event\Manager', [], [], '', false);
        $contextMock = $this->getMock('Magento\Framework\Model\Context', [], [], '', false);
        $contextMock->expects(
            $this->any()
        )->method(
            'getEventDispatcher'
        )->will(
            $this->returnValue($eventDispatcherMock)
        );

        $this->resourceMock = $this->getMock(
            'Magento\Framework\Model\Resource\AbstractResource',
            [
                '_construct',
                '_getReadAdapter',
                '_getWriteAdapter',
                'getIdFieldName',
                'beginTransaction',
                'save',
                'commit',
                'addCommitCallback',
                'rollBack',
            ],
            [],
            '',
            false
        );

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->domain = $helper->getObject(
            'Magento\Backend\Model\Config\Backend\Domain',
            [
                'context' => $contextMock,
                'resource' => $this->resourceMock,
            ]
        );
    }

    /**
     * @covers \Magento\Backend\Model\Config\Backend\Domain::_beforeSave
     * @dataProvider beforeSaveDataProvider
     *
     * @param string $value
     * @param string $exceptionMessage
     */
    public function testBeforeSave($value, $exceptionMessage = null)
    {
        $this->resourceMock->expects($this->any())->method('addCommitCallback')->will($this->returnSelf());
        $this->resourceMock->expects($this->any())->method('commit')->will($this->returnSelf());
        $this->resourceMock->expects($this->any())->method('rollBack')->will($this->returnSelf());

        $this->domain->setValue($value);
        try {
            $this->domain->save();
            if ($exceptionMessage ) {
                $this->fail('Failed to throw exception');
            }
        } catch (Exception $e) {
            $this->assertContains('Invalid domain name: ', $e->getMessage());
            $this->assertContains($exceptionMessage, $e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function beforeSaveDataProvider()
    {
        return [
            'not string' => [['array'], 'Invalid type given. String expected'],
            'invalid hostname' => [
                'http://',
                'The input does not match the expected structure for a DNS hostname; '
                . 'The input does not appear to be a valid URI hostname; '
                . 'The input does not appear to be a valid local network name'
            ],
            'valid hostname' => ['hostname.com'],
            'empty string' => [''],
        ];
    }
}
