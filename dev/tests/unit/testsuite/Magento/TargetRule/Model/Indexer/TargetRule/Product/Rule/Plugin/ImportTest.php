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

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Plugin\Import
     */
    protected $_model;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerMock;

    public function setUp()
    {
        $this->_indexerMock = $this->getMock('Magento\Indexer\Model\IndexerInterface');
        $this->_model = new \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Plugin\Import(
            $this->_indexerMock
        );
    }

    public function testAfterImportSource()
    {
        $subjectMock = $this->getMock('Magento\ImportExport\Model\Import', array(), array(), '', false);
        $result = 'result';

        $this->_indexerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(11));
        $this->_indexerMock->expects($this->once())
            ->method('invalidate');

        $this->assertEquals(
            $result,
            $this->_model->afterImportSource($subjectMock, $result)
        );
    }
}
