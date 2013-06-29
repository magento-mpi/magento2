<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Backend_Adminhtml_System_ConfigControllerTest extends Mage_Backend_Utility_Controller
{
    public function testEditAction()
    {
        $this->dispatch('backend/admin/system_config/edit');
        $this->assertContains('<ul id="system_config_tabs"', $this->getResponse()->getBody());
    }
}
