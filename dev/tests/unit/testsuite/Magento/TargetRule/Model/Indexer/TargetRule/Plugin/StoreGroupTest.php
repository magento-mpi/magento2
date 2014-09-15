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

namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

class StoreGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Plugin\StoreGroup
     */
    protected $_model;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleProductMock;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productRuleMock;

    public function setUp()
    {
        $this->_ruleProductMock = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor',
            [],
            [],
            '',
            false
        );
        $this->_productRuleMock = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor',
            [],
            [],
            '',
            false
        );
        $this->_model = new \Magento\TargetRule\Model\Indexer\TargetRule\Plugin\StoreGroup(
            $this->_productRuleMock,
            $this->_ruleProductMock
        );
    }

    public function testCategoryChanges()
    {
        $subjectMock = $this->getMock('\Magento\Store\Model\Resource\Group', array(), array(), '', false);
        $modelMock = $this->getMock('\Magento\Framework\Model\AbstractModel', array(), array(), '', false);

        $subjectMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue(11));

        $this->_productRuleMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->_ruleProductMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->_model->beforeSave($subjectMock, $modelMock);
    }
}
