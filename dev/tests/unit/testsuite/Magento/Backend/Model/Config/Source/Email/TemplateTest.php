<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Source_Email_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Config_Source_Email_Template
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Registry|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_Email_Template_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_emailConfig;

    protected function setUp()
    {
        $this->_coreRegistry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false, false);
        $this->_emailConfig = $this->getMock('Magento_Core_Model_Email_Template_Config', array(), array(), '', false);
        $this->_model = new Magento_Backend_Model_Config_Source_Email_Template(
            $this->_coreRegistry, $this->_emailConfig
        );
    }

    public function testToOptionArray()
    {
        $collection = $this->getMock(
            'Magento_Core_Model_Resource_Email_Template_Collection', array(), array(), '', false
        );
        $collection
            ->expects($this->once())
            ->method('toOptionArray')
            ->will($this->returnValue(array(
                array('value' => 'template_one', 'label' => 'Template One'),
                array('value' => 'template_two', 'label' => 'Template Two'),
            )))
        ;
        $this->_coreRegistry
            ->expects($this->once())
            ->method('registry')
            ->with('config_system_email_template')
            ->will($this->returnValue($collection))
        ;
        $this->_emailConfig
            ->expects($this->once())
            ->method('getTemplateLabel')
            ->with('template_new')
            ->will($this->returnValue('Template New'))
        ;
        $expectedResult = array(
            array('value' => 'template_new', 'label' => 'Template New (Default)'),
            array('value' => 'template_one', 'label' => 'Template One'),
            array('value' => 'template_two', 'label' => 'Template Two'),
        );
        $this->_model->setPath('template/new');
        $this->assertEquals($expectedResult, $this->_model->toOptionArray());
    }
}
