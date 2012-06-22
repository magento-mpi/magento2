<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Controller_ActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Controller_Action|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass(
            'Mage_Adminhtml_Controller_Action',
            array(new Magento_Test_Request(), new Magento_Test_Response())
        );
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('Mage_Backend_Controller_ActionAbstract', $this->_model);
    }

    /**
     * @covers  Mage_Adminhtml_Controller_Action::getUsedModuleName
     * @covers  Mage_Adminhtml_Controller_Action::setUsedModuleName
     */
    public function testUsedModuleName()
    {
        $this->assertEquals('adminhtml', $this->_model->getUsedModuleName());
        $this->_model->setUsedModuleName('dummy');
        $this->assertEquals('dummy', $this->_model->getUsedModuleName());
    }
}
