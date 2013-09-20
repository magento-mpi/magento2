<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Model_LayoutUpdate_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectHelper;

    protected function setUp()
    {
        $this->_objectHelper = new Magento_TestFramework_Helper_ObjectManager($this);
    }
    /**
     * @dataProvider isValidDataProvider
     * @param string $value
     * @param boolean $isValid
     * @param boolean $expectedResult
     */
    public function testIsValid($value, $isValid, $expectedResult)
    {
        $modulesReader = $this->getMockBuilder('Magento_Core_Model_Config_Modules_Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $modulesReader->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Core')
            ->will($this->returnValue('dummyDir'));

        $domConfigFactory = $this->getMockBuilder('Magento_Config_DomFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $domConfigFactory->expects($this->any())
            ->method('createDom')
            ->with(array(
                'xml'        => '<layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $value . '</layout>',
                'schemaFile' => 'dummyDir' . DIRECTORY_SEPARATOR . 'layouts.xsd'
            ))
            ->will(
                $isValid
                ? $this->returnSelf()
                : $this->throwException(new Magento_Config_Dom_ValidationException)
            );
        $domConfigFactory->expects($this->any())
            ->method('loadXml')
            ->with('<layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $value . '</layout>')
            ->will($this->returnSelf());

        $model = $this->_objectHelper->getObject('Magento_Adminhtml_Model_LayoutUpdate_Validator', array(
            'modulesReader' => $modulesReader,
            'domConfigFactory' => $domConfigFactory,
        ));

        $this->assertEquals($model->isValid($value), $expectedResult);
    }

    /**
     * @see self::testIsValid()
     * @return array
     */
    public function isValidDataProvider()
    {
        return array(
            array('test', true, true),
            array('test', false, false),
        );
    }
}
