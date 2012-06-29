<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_User_Adminhtml_UserController
 * @group module:Mage_Api2
 */
class Mage_Api2_Adminhtml_UserControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * Test that a block added by Api2 layout is present in content of action
     *
     * @return void
     */
    public function testNewActionTest()
    {
        $this->dispatch('admin/user/new');
        $this->assertContains('name="api2_roles_section"', $this->getResponse()->getBody());
    }
}
