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

class Mage_Adminhtml_Controller_ActionTest extends Mage_Backend_Area_TestCase
{
    /**
     * @var Mage_Adminhtml_Controller_Action|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $arguments = array(
            'request' => new Magento_Test_Request(),
            'response' => new Magento_Test_Response(),
        );
        $this->_model = $this->getMockForAbstractClass(
            'Mage_Adminhtml_Controller_Action',
            array(
                'context'         => Mage::getObjectManager()->create('Mage_Backend_Controller_Context', $arguments),
                'areaCode'        => 'adminhtml'
            )
        );
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
