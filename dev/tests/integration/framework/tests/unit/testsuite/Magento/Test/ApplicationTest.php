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

class Magento_Test_ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Magento_TestFramework_Application::getInstallDir()
     * @covers Magento_TestFramework_Application::getDbInstance()
     * @covers Magento_TestFramework_Application::getInitParams()
     */
    public function testConstructor()
    {
        $dbInstance = $this->getMockForAbstractClass('Magento_TestFramework_Db_DbAbstract', array(), '', false);
        $installDir = '/install/dir';
        $appMode = Magento_Core_Model_App_State::MODE_DEVELOPER;

        $object = new Magento_TestFramework_Application(
            $dbInstance,
            $installDir,
            new Magento_Simplexml_Element('<data/>'),
            '',
            array(),
            $appMode
        );

        $this->assertSame($dbInstance, $object->getDbInstance(), 'Db instance is not set in Application');
        $this->assertEquals($installDir, $object->getInstallDir(), 'Install directory is not set in Application');

        $initParams = $object->getInitParams();
        $this->assertInternalType('array', $initParams, 'Wrong initialization parameters type');
        $this->assertArrayHasKey(Magento_Core_Model_App::PARAM_APP_DIRS, $initParams, 'Directories are not configured');
        $this->assertArrayHasKey(Magento_Core_Model_App::PARAM_MODE, $initParams, 'Application mode is not configured');
        $this->assertEquals(
            Magento_Core_Model_App_State::MODE_DEVELOPER,
            $initParams[Magento_Core_Model_App::PARAM_MODE],
            'Wrong application mode configured'
        );
    }
}
