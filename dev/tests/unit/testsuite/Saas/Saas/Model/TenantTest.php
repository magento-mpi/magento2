<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Saas
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Saas_Model_TenantTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider loadModulesDataProvider
     */
    public function testLoadModulesFromString($modulesString, $expectedResult)
    {
        $method = new ReflectionMethod('Saas_Saas_Model_Tenant', '_loadModulesFromString');
        $method->setAccessible(true);
        $result = $method->invoke(null, $modulesString);
        $this->assertInternalType('array', $result);
        $this->assertEquals($result, $expectedResult);
    }

    public function loadModulesDataProvider()
    {
        return array(
            'empty_string' => array(
                '',
                array()
            ),
            'non_empty_string' => array(
                '<?xml version="1.0" encoding="utf-8" ?><config><modules>'
                    . '<Test_Module><active>true</active></Test_Module>'
                    . '<Test_Module1><active>false</active></Test_Module1>'
                    . '</modules></config>',
                array('Test_Module' => array('active' => 'true'), 'Test_Module1' => array('active' => 'false'))
            ),
        );
    }
}
