<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Combine;

class RootTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\Segment\Condition\Combine\Root
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configShare;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerSegment\Model\Segment\Condition\Combine\Root'
        );
    }

    /**
     * @dataProvider prepareConditionsSqlDataProvider
     * @param mixed $customer
     * @param int $website
     * @param array $expected
     */
    public function testPrepareConditionsSql($customer, $website, $expected)
    {
        $testMethod = new \ReflectionMethod($this->_model, '_prepareConditionsSql');
        $testMethod->setAccessible(true);

        $result = $testMethod->invoke($this->_model, $customer, $website);
        foreach ($expected as $part) {
            $this->assertContains($part, (string)$result, '', true);
        }
    }

    public function prepareConditionsSqlDataProvider()
    {
        return array(
            array(
                null,
                new \Zend_Db_Expr(1),
                array('`root`.`entity_id`', '`root`.`website_id`', 'where (website_id=1)')
            ),
            array(null, 2, array('`root`.`entity_id`', '`root`.`website_id`', 'where (website_id=2)')),
            array(1, 3, array('select 1'))
        );
    }
}
