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
class Saas_PrintedTemplate_Model_RelativeLengthTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Saas_PrintedTemplate_Model_RelativeLength::getValue
     */
    public function testGetValue()
    {
        $values = array(-1,0,1);
        foreach ($values as $v){
            $object = new Saas_PrintedTemplate_Model_RelativeLength($v);
            $this->assertInternalType('float', $object->getValue());
            $this->assertEquals($v, $object->getValue());
        }
    }

    /**
     * Test Saas_PrintedTemplate_Model_RelativeLength::__toString
     */
    public function testToString()
    {
        $object = new Saas_PrintedTemplate_Model_RelativeLength(1);
        $this->assertInternalType('string', $object->__toString());
    }

    /**
     * Test Saas_PrintedTemplate_Model_RelativeLength::getLength
     */
    public function testGetLength()
    {
        $base = new Zend_Measure_Length('10', Zend_Measure_Length::MILLIMETER);
        $object = new Saas_PrintedTemplate_Model_RelativeLength(50);

        $this->assertInstanceOf('Zend_Measure_Length', $object->getLength($base));
        $this->assertEquals('5 mm', $object->getLength($base)->toString());
    }
}
