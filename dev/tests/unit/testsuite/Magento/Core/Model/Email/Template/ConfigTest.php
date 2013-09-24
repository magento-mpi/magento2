<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Email_Template_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Email_Template_Config
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Email_Template_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataStorage;

    /**
     * @var Magento_Core_Model_Config_Modules_Reader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReader;

    protected function setUp()
    {
        $this->_dataStorage = $this->getMock(
            'Magento_Core_Model_Email_Template_Config_Data', array('getData'), array(), '', false
        );
        $this->_dataStorage
            ->expects($this->any())
            ->method('getData')
            ->will($this->returnValue(require __DIR__ . '/Config/_files/email_templates_merged.php'))
        ;
        $this->_moduleReader = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array('getModuleDir'), array(), '', false
        );
        $this->_model = new Magento_Core_Model_Email_Template_Config($this->_dataStorage, $this->_moduleReader);
    }

    public function testGetAvailableTemplates()
    {
        $this->assertEquals(array('template_one', 'template_two'), $this->_model->getAvailableTemplates());
    }

    public function testGetTemplateLabel()
    {
        $this->assertEquals('Template One', $this->_model->getTemplateLabel('template_one'));
    }

    public function testGetTemplateType()
    {
        $this->assertEquals('html', $this->_model->getTemplateType('template_one'));
    }

    public function testGetTemplateModule()
    {
        $this->assertEquals('Fixture_ModuleOne', $this->_model->getTemplateModule('template_one'));
    }

    public function testGetTemplateFilename()
    {
        $this->_moduleReader
            ->expects($this->once())
            ->method('getModuleDir')->with('view', 'Fixture_ModuleOne')
            ->will($this->returnValue('_files/Fixture/ModuleOne/view'))
        ;
        $actualResult = $this->_model->getTemplateFilename('template_one');
        $this->assertEquals('_files/Fixture/ModuleOne/view/email/one.html', $actualResult);
    }

    /**
     * @param string $getterMethod
     * @dataProvider getterMethodUnknownTemplateDataProvider
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Email template 'unknown' is not defined
     */
    public function testGetterMethodUnknownTemplate($getterMethod)
    {
        $this->_model->$getterMethod('unknown');
    }

    public function getterMethodUnknownTemplateDataProvider()
    {
        return array(
            'label getter'  => array('getTemplateLabel'),
            'type getter'   => array('getTemplateType'),
            'module getter' => array('getTemplateModule'),
            'file getter'   => array('getTemplateFilename'),
        );
    }

    /**
     * @param string $getterMethod
     * @param string $expectedException
     * @param array $fixtureFields
     * @dataProvider getterMethodUnknownFieldDataProvider
     */
    public function testGetterMethodUnknownField($getterMethod, $expectedException, array $fixtureFields = array())
    {
        $this->setExpectedException('UnexpectedValueException', $expectedException);
        $dataStorage = $this->getMock(
            'Magento_Core_Model_Email_Template_Config_Data', array('getData'), array(), '', false
        );
        $dataStorage
            ->expects($this->atLeastOnce())
            ->method('getData')
            ->will($this->returnValue(array('fixture' => $fixtureFields)))
        ;
        $model = new Magento_Core_Model_Email_Template_Config($dataStorage, $this->_moduleReader);
        $model->$getterMethod('fixture');
    }

    public function getterMethodUnknownFieldDataProvider()
    {
        return array(
            'label getter' => array(
                'getTemplateLabel',
                "Field 'label' is not defined for email template 'fixture'.",
            ),
            'type getter' => array(
                'getTemplateType',
                "Field 'type' is not defined for email template 'fixture'.",
            ),
            'module getter' => array(
                'getTemplateModule',
                "Field 'module' is not defined for email template 'fixture'.",
            ),
            'file getter, unknown module' => array(
                'getTemplateFilename',
                "Field 'module' is not defined for email template 'fixture'.",
            ),
            'file getter, unknown file' => array(
                'getTemplateFilename',
                "Field 'file' is not defined for email template 'fixture'.",
                array('module' => 'Fixture_Module')
            ),
        );
    }
}
