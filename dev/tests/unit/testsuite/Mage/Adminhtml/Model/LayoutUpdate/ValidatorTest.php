<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Model_LayoutUpdate_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectHelper;

    public function setUp()
    {
        $this->_objectHelper = new Magento_Test_Helper_ObjectManager($this);
    }
    /**
     * @dataProvider isValidDataProvider
     * @param string $value
     * @param boolean $isValid
     * @param boolean $expectedResult
     */
    public function testIsValid($value, $isValid, $expectedResult)
    {
        $modulesReader = $this->getMockBuilder('Mage_Core_Model_Config_Modules_Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $modulesReader->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Mage_Core')
            ->will($this->returnValue('dummyDir'));

        $domConfig = $this->getMockBuilder('Magento_Config_Dom')
            ->disableOriginalConstructor()
            ->getMock();
        $domConfig->expects($this->any())
            ->method('setSchemaFile')
            ->with('dummyDir' . DIRECTORY_SEPARATOR .  'layouts.xsd')
            ->will(
                $isValid
                ? $this->returnSelf()
                : $this->throwException(new Magento_Config_Dom_ValidationException)
            );
        $domConfig->expects($this->any())
            ->method('loadXml')
            ->with('<layout>' . $value . '</layout>')
            ->will($this->returnSelf());

        $model = $this->_objectHelper->getObject('Mage_Adminhtml_Model_LayoutUpdate_Validator', array(
            'modulesReader' => $modulesReader,
            'domConfig' => $domConfig,
        ));

        $this->assertEquals($model->isValid($value), $expectedResult);
        if ($isValid) {
            $this->assertInstanceOf('Magento_Simplexml_Element', $model->value);
        }
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
