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

namespace Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Plugin;

class CustomerSegmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Plugin\CustomerSegment
     */
    protected $_model;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerMock;

    public function setUp()
    {
        $this->_indexerMock = $this->getMock('Magento\Indexer\Model\IndexerInterface');
        $this->_model = new \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Plugin\CustomerSegment(
            $this->_indexerMock
        );
    }

    public function testCustomerSegmentChanges()
    {
        $subjectMock = $this->getMock('Magento\CustomerSegment\Model\Segment', array(), array(), '', false);

        $this->_indexerMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(11));

        $this->_indexerMock->expects($this->at(2))
            ->method('invalidate');

        $this->assertEquals(
            $subjectMock,
            $this->_model->afterDelete($subjectMock)
        );

        $this->assertEquals(
            $subjectMock,
            $this->_model->afterSave($subjectMock)
        );
    }
}
