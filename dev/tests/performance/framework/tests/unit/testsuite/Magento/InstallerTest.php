<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_InstallerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Shell|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shell;

    /**
     * @var Magento_Installer|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var
     */
    protected $_installerScript;

    protected function setUp()
    {
        $this->_shell = $this->getMock('Magento_Shell', array('execute'));
        $this->_installerScript = realpath(__DIR__ . '/_files/install_stub.php');
        $this->_object = $this->getMock(
            'Magento_Installer',
            array('_bootstrap', '_reindex'),
            array($this->_installerScript, $this->_shell)
        );
    }

    protected function tearDown()
    {
        unset($this->_shell);
        unset($this->_object);
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Console installer 'non_existing_script' does not exist.
     */
    public function testConstructorException()
    {
        new Magento_Installer('non_existing_script', $this->_shell);
    }

    public function testUninstall()
    {
        $this->_shell
            ->expects($this->once())
            ->method('execute')
            ->with('php -f %s -- --uninstall', array($this->_installerScript))
        ;
        $this->_object->uninstall();
    }

    public function testInstall()
    {
        $this->_shell
            ->expects($this->once())
            ->method('execute')
            ->with('php -f %s -- --option1 %s --option2 %s', array($this->_installerScript, 'value1', 'value 2'))
        ;
        $this->_object
            ->expects($this->once())
            ->method('_bootstrap')
        ;
        $this->_object
            ->expects($this->once())
            ->method('_reindex')
        ;
        $this->_object->install(array('option1' => 'value1', 'option2' => 'value 2'));
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Fixture has been applied
     */
    public function testInstallFixtures()
    {
        $this->_object->install(array(), array(__DIR__ . '/_files/fixture.php'));
    }
}
