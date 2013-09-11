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

    public function setUp()
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
        $modulesReader = $this->getMockBuilder('Magento\Core\Model\Config\Modules\Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $modulesReader->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Core')
            ->will($this->returnValue('dummyDir'));

        $domConfigFactory = $this->getMockBuilder('Magento\Config\DomFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $params = array(
            'xml' => '<layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
                . '<handle id="handleId">' . trim($value) . '</handle>'
                . '</layout>',
            'schemaFile' => 'dummyDir' . DIRECTORY_SEPARATOR .  'layouts.xsd'
        );

        $domConfigFactory->expects($this->once())
            ->method('createDom')
            ->with($this->equalTo($params))
            ->will(
                $isValid
                ? $this->returnSelf()
                : $this->throwException(new \Magento\Config\Dom\ValidationException)
            );

        $model = $this->_objectHelper->getObject('\Magento\Adminhtml\Model\LayoutUpdate\Validator', array(
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
