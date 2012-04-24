<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_User
 */
class Mage_User_Adminhtml_UserControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @covers Mage_Adminhtml_Controller_Action::_addContent
     */
    public function testIndexAction()
    {
        $this->dispatch('admin/user/index');
        $this->assertStringMatchesFormat('%a<div class="content-header">%aUsers%a', $this->getResponse()->getBody());
    }
}
