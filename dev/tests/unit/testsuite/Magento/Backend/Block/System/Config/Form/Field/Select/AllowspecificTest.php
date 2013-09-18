<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Block_System_Config_Form_Field_Select_AllowspecificTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Block_System_Config_Form_Field_Select_Allowspecific
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formMock;

    protected function setUp()
    {
        $testHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_object = $testHelper->getObject('Magento_Backend_Block_System_Config_Form_Field_Select_Allowspecific');
        $this->_object->setData('html_id', 'spec_element');
        $this->_formMock = $this->getMock('Magento_Data_Form',
            array('getHtmlIdPrefix', 'getHtmlIdSuffix', 'getElement'),
            array(), '', false, false
        );
    }

    public function testGetAfterElementHtml()
    {
        $this->_formMock->expects($this->exactly(2))
            ->method('getHtmlIdPrefix')->will($this->returnValue('test_prefix_'));
        $this->_formMock->expects($this->exactly(2))
            ->method('getHtmlIdSuffix')->will($this->returnValue('_test_suffix'));

        $afterHtmlCode = 'after html';
        $this->_object->setData('after_element_html', $afterHtmlCode);
        $this->_object->setForm($this->_formMock);

        $actual = $this->_object->getAfterElementHtml();

        $this->assertStringEndsWith($afterHtmlCode, $actual);
        $this->assertStringStartsWith('<script type="text/javascript">', trim($actual));
        $this->assertContains('test_prefix_spec_element_test_suffix', $actual);
    }

    /**
     * @param $value
     * @dataProvider getHtmlWhenValueIsEmptyDataProvider
     */
    public function testGetHtmlWhenValueIsEmpty($value)
    {
        $this->_object->setForm($this->_formMock);

        $elementMock = $this->getMock('Magento_Data_Form_Element_Select',
            array('setDisabled'), array(), '', false, false
        );

        $elementMock->expects($this->once())->method('setDisabled')->with('disabled');
        $countryId = 'tetst_county_specificcountry';
        $this->_object->setId('tetst_county_allowspecific');
        $this->_formMock->expects($this->once())->method('getElement')->with($countryId)
            ->will($this->returnValue($elementMock));

        $this->_object->setValue($value);
        $this->assertNotEmpty($this->_object->getHtml());
    }

    public function getHtmlWhenValueIsEmptyDataProvider()
    {
        return array(
            'zero' => array('1' => 0),
            'null' => array('1' => null),
            'false' => array('1' => false),
            'negative' => array('1' => -1),
        );
    }

}
