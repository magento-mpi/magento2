<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Validator_ConstraintAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testAddGetErrors()
    {
        $expectedErrors = array(
            'test_field' => array(
                'test error #1',
                'test error #2',
            )
        );
        /** @var Magento_Validator_ConstraintAbstract $constraint */
        $constraint = $this->getMockForAbstractClass('Magento_Validator_ConstraintAbstract');
        $constraint->addError('test_field', 'test error #1');
        $constraint->addError('test_field', 'test error #2');

        $this->assertEquals($expectedErrors, $constraint->getErrors());
    }
}