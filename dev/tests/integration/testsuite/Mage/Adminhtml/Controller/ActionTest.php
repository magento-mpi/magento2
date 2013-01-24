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

class Mage_Adminhtml_Controller_ActionTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Adminhtml_Controller_Action|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass(
            'Mage_Adminhtml_Controller_Action',
            array(
                'request'         => new Magento_Test_Request(),
                'response'        => new Magento_Test_Response(),
                'areaCode'        => 'adminhtml',
                'objectManager'   => Mage::getObjectManager(),
                'frontController' => Mage::getObjectManager()->get('Mage_Core_Controller_Varien_Front'),
                'layoutFactory'   => Mage::getObjectManager()->get('Mage_Core_Model_Layout_Factory')
            )
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
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
