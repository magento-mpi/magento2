<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Rule\Model\Condition\Product;

class AbstractProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractProduct|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_condition;

    public function setUp()
    {
        $this->_condition = $this->getMockForAbstractClass(
            '\Magento\Rule\Model\Condition\Product\AbstractProduct',
            [],
            '',
            false
        );
    }

    public function testGetjointTables()
    {
        $this->_condition->setAttribute('category_ids');
        $this->assertEquals([], $this->_condition->getTablesToJoin());
    }

    public function testGetMappedSqlField()
    {
        $this->_condition->setAttribute('category_ids');
        $this->assertEquals('e.entity_id', $this->_condition->getMappedSqlField());
    }
}
