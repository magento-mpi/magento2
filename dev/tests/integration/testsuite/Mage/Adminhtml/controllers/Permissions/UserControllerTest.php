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
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Permissions_UserControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @covers Mage_Adminhtml_Controller_Action::_addContent
     */
    public function testIndexAction()
    {
        $this->dispatch('admin/permissions_user/index');
        $this->assertStringMatchesFormat('%a<div class="content-header">%aUsers%a', $this->getResponse()->getBody());
    }
}
