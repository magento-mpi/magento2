<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Backend_BaseurlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $value
     * @magentoDbIsolation enabled
     * @dataProvider validationDataProvider
     */
    public function testValidation($value)
    {
        /** @var $model Mage_Backend_Model_Config_Backend_Baseurl */
        $model = Mage::getModel('Mage_Backend_Model_Config_Backend_Baseurl');
        $model->setValue($value)->save();
        $this->assertNotEmpty((int)$model->getId());
    }

    /**
     * @return array
     */
    public function validationDataProvider()
    {
        return array(
            array(''),
            array('{{a_placeholder}}'),
            array('http://example.com/'),
            array('http://example.com/uri/'),
        );
    }

    /**
     * @param string $value
     * @magentoDbIsolation enabled
     * @expectedException Mage_Core_Exception
     * @dataProvider validationExceptionDataProvider
     */
    public function testValidationException($value)
    {
        /** @var $model Mage_Backend_Model_Config_Backend_Baseurl */
        $model = Mage::getModel('Mage_Backend_Model_Config_Backend_Baseurl');
        $model->setValue($value)->save();
    }

    /**
     * @return array
     */
    public function validationExceptionDataProvider()
    {
        return array(
            array('not a valid URL'),
            array('example.com'),
            array('http://example.com'),
            array('http://example.com/uri'),
        );
    }
}
