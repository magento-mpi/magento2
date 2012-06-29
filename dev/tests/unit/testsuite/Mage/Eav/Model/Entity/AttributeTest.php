<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Eav_Model_Entity_AttributeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $givenFrontendInput
     * @param string $expectedBackendType
     * @dataProvider dataGetBackendTypeByInput
     */
    public function testGetBackendTypeByInput($givenFrontendInput, $expectedBackendType)
    {
        $model = $this->getMock('Mage_Eav_Model_Entity_Attribute', null, array(), '', false);
        $this->assertEquals($expectedBackendType, $model->getBackendTypeByInput($givenFrontendInput));
    }

    public static function dataGetBackendTypeByInput()
    {
        return array(
            array('unrecognized-frontent-input', null),
            array('text', 'varchar'),
            array('gallery', 'varchar'),
            array('media_image', 'varchar'),
            array('multiselect', 'varchar'),
            array('image', 'text'),
            array('textarea', 'text'),
            array('date', 'datetime'),
            array('select', 'int'),
            array('boolean', 'int'),
            array('price', 'decimal'),
            array('weight', 'decimal'),
        );
    }

    /**
     * @param string $givenFrontendInput
     * @param string $expectedDefaultValue
     * @dataProvider dataGetDefaultValueByInput
     */
    public function testGetDefaultValueByInput($givenFrontendInput, $expectedDefaultValue)
    {
        $model = $this->getMock('Mage_Eav_Model_Entity_Attribute', null, array(), '', false);
        $this->assertEquals($expectedDefaultValue, $model->getDefaultValueByInput($givenFrontendInput));
    }

    public static function dataGetDefaultValueByInput()
    {
        return array(
            array('unrecognized-frontent-input', ''),
            array('select', ''),
            array('gallery', ''),
            array('media_image', ''),
            array('multiselect', null),
            array('text', 'default_value_text'),
            array('price', 'default_value_text'),
            array('image', 'default_value_text'),
            array('weight', 'default_value_text'),
            array('textarea', 'default_value_textarea'),
            array('date', 'default_value_date'),
            array('boolean', 'default_value_yesno'),
        );
    }
}
