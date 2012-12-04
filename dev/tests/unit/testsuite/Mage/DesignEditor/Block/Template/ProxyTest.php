<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Template_ProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * Template proxy
     *
     * @var Mage_DesignEditor_Block_Template_Proxy
     */
    protected $_template;

    /**
     * Test data
     *
     * @var array
     */
    protected $_testData = array(
        'param1' => 'value1',
        'param2' => 'value2'
    );

    protected function setUp()
    {
        $template = $this->getMock('Mage_DesignEditor_Block_Template', array('setData'), array(), '', false);
        $template->expects($this->once())
            ->method('setData')
            ->with($this->_testData);

        $objectMagenger = $this->getMock('Magento_ObjectManager_Zend', array('get'), array(), '', false);
        $objectMagenger->expects($this->once())
            ->method('get')
            ->with(Mage_DesignEditor_Block_Template_Proxy::ENTITY_CLASS)
            ->will($this->returnValue($template));

        $this->_template = new Mage_DesignEditor_Block_Template_Proxy($objectMagenger);
    }

    protected function tearDown()
    {
        unset($this->_template);
    }

    /**
     * @covers Mage_DesignEditor_Block_Template_Proxy::_getTemplate
     */
    public function testSetData()
    {
        $this->_template->setData($this->_testData);
    }
}
