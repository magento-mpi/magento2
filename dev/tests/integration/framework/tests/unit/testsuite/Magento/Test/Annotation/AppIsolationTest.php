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
 * Test class for \Magento\TestFramework\Annotation\AppIsolation.
 */
namespace Magento\Test\Annotation;

class AppIsolationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Annotation\AppIsolation
     */
    protected $_object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_application;

    protected function setUp()
    {
        $this->_application = $this->getMock(
            'Magento\TestFramework\Application', array('reinitialize'), array(), '', false);
        $this->_object = new \Magento\TestFramework\Annotation\AppIsolation($this->_application);
    }

    protected function tearDown()
    {
        $this->_application = null;
        $this->_object = null;
    }

    public function testStartTestSuite()
    {
        $this->_application->expects($this->once())->method('reinitialize');
        $this->_object->startTestSuite();
    }

    /**
     * @magentoAppIsolation invalid
     * @expectedException \Magento\Exception
     */
    public function testEndTestIsolationInvalid()
    {
        $this->_object->endTest($this);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppIsolation disabled
     * @expectedException \Magento\Exception
     */
    public function testEndTestIsolationAmbiguous()
    {
        $this->_object->endTest($this);
    }

    public function testEndTestIsolationDefault()
    {
        $this->_application->expects($this->never())->method('reinitialize');
        $this->_object->endTest($this);
    }

    public function testEndTestIsolationController()
    {
        /** @var $controllerTest \Magento\TestFramework\TestCase\ControllerAbstract */
        $controllerTest = $this->getMockForAbstractClass('Magento\TestFramework\TestCase\ControllerAbstract');
        $this->_application->expects($this->once())->method('reinitialize');
        $this->_object->endTest($controllerTest);
    }

    /**
     * @magentoAppIsolation disabled
     */
    public function testEndTestIsolationDisabled()
    {
        $this->_application->expects($this->never())->method('reinitialize');
        $this->_object->endTest($this);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testEndTestIsolationEnabled()
    {
        $this->_application->expects($this->once())->method('reinitialize');
        $this->_object->endTest($this);
    }
}
