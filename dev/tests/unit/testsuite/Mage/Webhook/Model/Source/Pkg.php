<?php
/**
 * Parent class for Source tests that provides common functionality.
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Source_Pkg extends PHPUnit_Framework_TestCase
{
    /** Config values */
    const CONFIG_LABEL = 'blah';
    const CONFIG_STATUS = 'enabled';
    const TRANSLATED = '_translated';

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockTranslate;
    
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockConfig;
    
    /** @var Mage_Core_Model_Config_Element */
    protected $_modelConfigElement;
    
    public function setUp()
    {
        $label = self::CONFIG_LABEL;
        $status = self::CONFIG_STATUS;
        $this->_modelConfigElement = new Mage_Core_Model_Config_Element(
            "<types><type><status>{$status}</status><label>{$label}</label></type></types>"
        );
        $this->_mockConfig = $this->getMockBuilder('Mage_Core_Model_Config')
            ->disableOriginalConstructor()->getMock();
        $this->_mockConfig->expects($this->any())
            ->method('getNode')
            ->will($this->returnValue($this->_modelConfigElement));
        $this->_mockTranslate = $this->getMockBuilder('Mage_Core_Model_Translate')
            ->disableOriginalConstructor()->getMock();
        $this->_mockTranslate->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(
                function ($args) {
                    return array_shift($args) . Mage_Webhook_Model_Source_Pkg::TRANSLATED;
                }
            ));
    }

    /**
     * Asserts that the elements array contains the expected label and value.
     *
     * @param $elements
     */
    protected function _assertElements($elements)
    {
        $this->assertSame(self::CONFIG_LABEL . self::TRANSLATED, $elements[0]['label']);
        $this->assertSame('type', $elements[0]['value']);
    }
}