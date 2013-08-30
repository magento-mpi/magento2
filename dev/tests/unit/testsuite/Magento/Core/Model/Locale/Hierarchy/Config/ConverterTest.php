<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Locale_Hierarchy_Config_ConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Locale_Hierarchy_Config_Converter
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Locale_Hierarchy_Config_Converter();
    }

    /**
     * @dataProvider composeLocaleHierarchyDataProvider
     */
    public function testComposeLocaleHierarchy($localeConfig, $localeHierarchy)
    {
        $dom = new DOMDocument();
        $dom->loadXML($localeConfig);
        $this->assertEquals($localeHierarchy, $this->_model->convert($dom));
    }

    public function composeLocaleHierarchyDataProvider()
    {
        return array(
            array(
                'xml' => '<config><locale code="en_US" parent="en_UK" /><locale code="en_UK" parent="pt_BR"/></config>',
                array(
                    'en_US' => array('pt_BR', 'en_UK'),
                    'en_UK' => array('pt_BR'),
                )
            ),
            array(
                'xml' => '<config><locale code="en_US" parent="en_UK"/><locale code="en_UK" parent="en_US"/></config>',
                array(
                    'en_US' => array('en_UK'),
                    'en_UK' => array('en_US'),
                )
            ),
            array(
                'xml' => '<config></config>',
                array()
            ),
        );
    }
}
