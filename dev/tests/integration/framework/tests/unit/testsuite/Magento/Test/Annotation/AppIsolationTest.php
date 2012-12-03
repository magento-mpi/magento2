<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Test_Annotation_AppIsolation.
 */
class Magento_Test_Annotation_AppIsolationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Annotation_AppIsolation|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = $this->getMock('Magento_Test_Annotation_AppIsolation', array('_isolateApp'));
    }

    public function testStartTestSuite()
    {
        $this->_object->expects($this->once())->method('_isolateApp');
        $this->_object->startTestSuite();
    }

    /**
     * @magentoAppIsolation invalid
     * @expectedException Magento_Exception
     */
    public function testEndTestIsolationInvalid()
    {
        $this->_object->endTest($this);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppIsolation disabled
     * @expectedException Magento_Exception
     */
    public function testEndTestIsolationAmbiguous()
    {
        $this->_object->endTest($this);
    }

    public function testEndTestIsolationDefault()
    {
        $this->_object->expects($this->never())->method('_isolateApp');
        $this->_object->endTest($this);
    }

    public function testEndTestIsolationController()
    {
        /** @var $controllerTest Magento_Test_TestCase_ControllerAbstract */
        $controllerTest = $this->getMockForAbstractClass('Magento_Test_TestCase_ControllerAbstract');
        $this->_object->expects($this->once())->method('_isolateApp');
        $this->_object->endTest($controllerTest);
    }

    /**
     * @magentoAppIsolation disabled
     */
    public function testEndTestIsolationDisabled()
    {
        $this->_object->expects($this->never())->method('_isolateApp');
        $this->_object->endTest($this);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testEndTestIsolationEnabled()
    {
        $this->_object->expects($this->once())->method('_isolateApp');
        $this->_object->endTest($this);
    }
}
