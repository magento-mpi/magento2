<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_AppTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_App|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $objectManager = new Magento_ObjectManager_Zend();

        $dirs = new Mage_Core_Model_Dir(__DIR__, array(), array(Mage_Core_Model_Dir::CONFIG => __DIR__));
        $objectManager->addSharedInstance($dirs, 'Mage_Core_Model_Dir');

        $this->_model = $this->getMock(
            'Mage_Core_Model_App',
            array('_initEnvironment', '_initFilesystem', '_initLogger', '_initCache'),
            array($objectManager)
        );
        $objectManager->addSharedInstance($this->_model, 'Mage_Core_Model_App');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testIsInstalledFalse()
    {
        $this->_model->baseInit(array(
            Mage_Core_Model_Config::INIT_OPTION_EXTRA_DATA
                => sprintf(Mage_Core_Model_Config::CONFIG_TEMPLATE_INSTALL_DATE, 'invalid')
        ));
        $this->assertFalse($this->_model->isInstalled());
    }

    public function testIsInstalledTrue()
    {
        $this->_model->baseInit(array(
            Mage_Core_Model_Config::INIT_OPTION_EXTRA_DATA
                => sprintf(Mage_Core_Model_Config::CONFIG_TEMPLATE_INSTALL_DATE, 'Fri, 28 Dec 2012 11:29:51 -0800')
        ));
        $this->assertTrue($this->_model->isInstalled());
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Application is not installed yet, please complete the installation first.
     */
    public function testRequireInstalledInstance()
    {
        $this->_model->baseInit(array(
            Mage_Core_Model_Config::INIT_OPTION_EXTRA_DATA
                => sprintf(Mage_Core_Model_Config::CONFIG_TEMPLATE_INSTALL_DATE, 'invalid')
        ));
        $this->_model->requireInstalledInstance();
    }
}
