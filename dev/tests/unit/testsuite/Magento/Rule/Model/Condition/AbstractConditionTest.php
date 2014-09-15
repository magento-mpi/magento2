<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Rule\Model\Condition;

class AbstractConditionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractCondition|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_condition;

    public function setUp()
    {
        $this->_condition = $this->getMockForAbstractClass(
            '\Magento\Rule\Model\Condition\AbstractCondition',
            [],
            '',
            false
        );
    }

    public function testGetjointTables()
    {
        $this->_condition->setAttribute('category_ids');
        $this->assertEquals([], $this->_condition->getTablesToJoin());
        $this->_condition->setAttribute('gdsjkfghksldjfg');
        $this->assertEmpty($this->_condition->getTablesToJoin());
    }

    public function testGetMappedSqlField()
    {
        $this->_condition->setAttribute('category_ids');
        $this->assertEquals('category_ids', $this->_condition->getMappedSqlField());
    }
}
