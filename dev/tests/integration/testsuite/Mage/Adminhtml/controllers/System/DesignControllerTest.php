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

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_System_DesignControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * @covers Mage_Adminhtml_Controller_Action::_addLeft
     */
    public function testEditAction()
    {
        $this->dispatch('backend/admin/system_design/edit');
        $this->assertStringMatchesFormat('%A<a%Aid="design_tabs_general"%A', $this->getResponse()->getBody());
    }
}
