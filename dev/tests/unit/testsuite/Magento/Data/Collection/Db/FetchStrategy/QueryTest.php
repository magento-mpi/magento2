<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Data\Collection\Db\FetchStrategy;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function testFetchAll()
    {
        $expectedResult = new \stdClass();
        $bindParams = array('param_one' => 'value_one', 'param_two' => 'value_two');
        $adapter = $this->getMockForAbstractClass(
            'Zend_Db_Adapter_Abstract',
            array(),
            '',
            false,
            true,
            true,
            array('fetchAll')
        );
        $select = new \Zend_Db_Select($adapter);
        $adapter->expects(
            $this->once()
        )->method(
            'fetchAll'
        )->with(
            $select,
            $bindParams
        )->will(
            $this->returnValue($expectedResult)
        );
        $object = new \Magento\Data\Collection\Db\FetchStrategy\Query();
        $this->assertSame($expectedResult, $object->fetchAll($select, $bindParams));
    }
}
