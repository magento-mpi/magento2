<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reminder\Model\Rule\Condition;

class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reminder\Model\Rule\Condition\Cart
     */
    protected $_model;

    /**
     * @dataProvider daysDiffConditionDataProvider
     */
    public function testDaysDiffCondition($operator, $value, $expectedResult)
    {
        $dateModelMock = $this->getMock('Magento\Core\Model\Date', array('gmtDate'), array(), '', false);
        $dateModelMock->expects($this->atLeastOnce())->method('gmtDate')->will($this->returnValue('2013-12-24'));

        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Reminder\Model\Rule\Condition\Cart', array(
            'dateModel' => $dateModelMock
        ));
        $this->_model->setOperator($operator);
        $this->_model->setValue($value);

        $where = $this->_model->getConditionsSql(0, 0)->getPart('where');
        $this->assertContains($expectedResult, $where[1]);
    }

    /**
     * @return array
     */
    public function daysDiffConditionDataProvider()
    {
        return array(
            array('>=', '1', 'AND ((TO_DAYS(\'2013-12-24 00:00:00\') - TO_DAYS(quote.updated_at)) >= 1)'),
            array('>', '1', 'AND ((TO_DAYS(\'2013-12-24 00:00:00\') - TO_DAYS(quote.updated_at)) > 1)'),
            array('>=', '0', 'AND ((TO_DAYS(\'2013-12-24 00:00:00\') - TO_DAYS(quote.updated_at)) >= 0)'),
            array('>', '0', 'AND ((TO_DAYS(\'2013-12-24 00:00:00\') - TO_DAYS(quote.updated_at)) > 0)'),
        );
    }

    /**
     * @expectedException \Magento\Core\Exception
     */
    public function testDaysDiffConditionException()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Reminder\Model\Rule\Condition\Cart');
        $this->_model->setOperator('');
        $this->_model->setValue(-1);
        $this->_model->getConditionsSql(0, 0);
    }
}
