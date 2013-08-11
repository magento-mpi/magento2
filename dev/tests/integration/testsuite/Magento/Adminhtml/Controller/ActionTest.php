<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Controller_ActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Adminhtml_Controller_Action|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $arguments = array(
            'request' => new Magento_TestFramework_Request(),
            'response' => new Magento_TestFramework_Response(),
        );
        $this->_model = $this->getMockForAbstractClass(
            'Magento_Adminhtml_Controller_Action',
            array(
                'context'         => Mage::getObjectManager()->create('Magento_Backend_Controller_Context', $arguments),
                'areaCode'        => 'adminhtml'
            )
        );
    }

    /**
     * @covers  Magento_Adminhtml_Controller_Action::getUsedModuleName
     * @covers  Magento_Adminhtml_Controller_Action::setUsedModuleName
     */
    public function testUsedModuleName()
    {
        $this->assertEquals('adminhtml', $this->_model->getUsedModuleName());
        $this->_model->setUsedModuleName('dummy');
        $this->assertEquals('dummy', $this->_model->getUsedModuleName());
    }
}
