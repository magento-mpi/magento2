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

class Magento_Reminder_Model_Rule_Condition_CartTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider daysDiffConditionDataProvider
     */
    public function testDaysDiffCondition($operator, $value, $expectedResult, $checkGmtDate = false)
    {
        $dateModelMock = $this->getMock('Magento_Core_Model_Date', array(), array(), '', false);
        if ($checkGmtDate) {
            $dateModelMock->expects($this->at(1))->method('gmtDate')->with();
        }
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Reminder_Model_Rule_Condition_Cart', array(
            'dateModel' => $dateModelMock
        ));
        $this->_model->setOperator($operator);
        $this->_model->setValue($value);

        $where = $this->_model->getConditionsSql(0, 0)->getPart('where');
        $this->assertContains($expectedResult, $where[1]);
    }

    /**
     * @see self::testDaysDiffCondition
     * @return array
     */
    public function daysDiffConditionDataProvider()
    {
        return array(
            array('>=', '1', 'TO_DAYS(quote.updated_at)) >= 1'),
            array('>', '1', 'TO_DAYS(quote.updated_at)) > 1'),
            array('>=', '0', 'TO_DAYS(quote.updated_at)) >= 0', true),
            array('>', '0', 'TO_DAYS(quote.updated_at)) > 0'),
        );
    }
}
