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
        $dateModelMock->expects($this->atLeastOnce())->method('gmtDate')->will($this->onConsecutiveCalls('11', '22'));

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
            array('>=', '1', 'AND (UNIX_TIMESTAMP(\'11\' - INTERVAL 0 DAY) >= UNIX_TIMESTAMP(quote.updated_at))'),
            array('>', '1', 'AND (UNIX_TIMESTAMP(\'11\' - INTERVAL 1 DAY) > UNIX_TIMESTAMP(quote.updated_at))'),
            array('>=', '0', 'AND (UNIX_TIMESTAMP(\'22\' - INTERVAL 0 DAY) >= UNIX_TIMESTAMP(quote.updated_at))'),
            array('>', '0', 'AND (UNIX_TIMESTAMP(\'11\' - INTERVAL 0 DAY) > UNIX_TIMESTAMP(quote.updated_at))'),
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
