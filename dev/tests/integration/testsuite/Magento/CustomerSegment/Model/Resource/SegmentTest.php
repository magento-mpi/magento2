<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerSegment_Model_Resource_SegmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createConditionSqlDataProvider
     */
    public function testCreateConditionSql($field, $operator, $value, $expected)
    {
        $segment = Mage::getResourceModel('Magento_CustomerSegment_Model_Resource_Segment');
        $result = $segment->createConditionSql($field, $operator, $value);
        $this->assertEquals($expected, $result);
    }

    /**
     * @see self::testCreateConditionSql()
     * @return array
     */
    public function createConditionSqlDataProvider()
    {
        return array(
            'Operator is' => array(
                'value', '==', '90064', "value = '90064'"
            ),
            'Operator is multiple values' => array(
                'value', '==', '90064,90065', "value IN ('90064', '90065')"
            ),
            'Operator is not' => array(
                'value', '!=', '90064', "value <> '90064'"
            ),
            'Operator is not multiple values' => array(
                'value', '!=', '90064,90065', "value NOT IN ('90064', '90065')"
            ),
            'Operator contains' => array(
                'value', '{}', '90064', "value LIKE '%90064%'"
            ),
            'Operator contains multiple values' => array(
                'value', '{}', '90064,90065', "value LIKE '%90064%' AND value LIKE '%90065%'"
            ),
            'Operator does not contain' => array(
                'value', '!{}', '90064', "value NOT LIKE '%90064%'"
            ),
            'Operator does not contain multiple values' => array(
                'value', '!{}', '90064,90065', "value NOT LIKE '%90064%' AND value NOT LIKE '%90065%'"
            ),
        );
    }
}
