<?php
/**
 * Parent class for Source tests that provides common functionality.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Source_Pkg extends PHPUnit_Framework_TestCase
{
    /** Config values */
    const CONFIG_LABEL = 'blah';
    const CONFIG_STATUS = 'enabled';

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockConfig;
    
    /** @var Magento_Core_Model_Config_Element */
    protected $_modelConfigElement;
    
    public function setUp()
    {
        $label = self::CONFIG_LABEL;
        $status = self::CONFIG_STATUS;
        $this->_modelConfigElement = new Magento_Core_Model_Config_Element(
            "<types><type><status>{$status}</status><label>{$label}</label></type></types>"
        );
        $this->_mockConfig = $this->getMockBuilder('Magento_Core_Model_Config')
            ->disableOriginalConstructor()->getMock();
        $this->_mockConfig->expects($this->any())
            ->method('getNode')
            ->will($this->returnValue($this->_modelConfigElement));
    }

    /**
     * Asserts that the elements array contains the expected label and value.
     *
     * @param $elements
     */
    protected function _assertElements($elements)
    {
        /** @var Magento_Phrase $phrase */
        $phrase = $elements[0]['label'];
        $this->assertSame(self::CONFIG_LABEL, $phrase->render());
        $this->assertSame('type', $elements[0]['value']);
    }
}
