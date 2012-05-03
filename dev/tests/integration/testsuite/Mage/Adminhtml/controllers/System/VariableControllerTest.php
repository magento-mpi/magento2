<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_System_VariableControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @covers Mage_Adminhtml_Controller_Action::_addLeft
     */
    public function testEditAction()
    {
        $this->dispatch('admin/system_variable/edit');
        $body = $this->getResponse()->getBody();
        $this->assertContains('function toggleValueElement(element) {', $body);
    }
}
