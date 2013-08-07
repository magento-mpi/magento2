<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Saas_PrintedTemplate_Model_Config::getVariablesArray
     */
    public function testGetVariablesArray()
    {
        $variablesArray = array('invoice','creditmemo','shipment');
        $config = $this->getMockBuilder('Saas_PrintedTemplate_Model_Config')
            ->setMethods(array('getConfigSectionArray'))
            ->getMock();

        $file = file_get_contents(__DIR__ . '/../_files/config.xml');
        $xml = simplexml_load_string($file, 'Magento_Simplexml_Element');
        $array = $xml->asArray();

        $config->expects($this->any())
            ->method('getConfigSectionArray')
            ->with($this->equalTo('variables'))
            ->will($this->returnValue($array['variables']));

        foreach ($variablesArray as $variable) {
            $variables = $config->getVariablesArray($variable);
            $this->assertArrayHasKey($variable, $variables);
            $diffArray = array_diff($variablesArray, array($variable));
            foreach ($diffArray as $var) {
                $this->assertFalse(array_key_exists($var, $variables));
            }
        }
    }
}
