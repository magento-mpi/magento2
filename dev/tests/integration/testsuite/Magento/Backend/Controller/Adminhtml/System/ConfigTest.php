<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Controller_Adminhtml_System_ConfigTest extends Magento_Backend_Utility_Controller
{
    public function testEditAction()
    {
        $this->dispatch('backend/admin/system_config/edit');
        $this->assertContains('<ul id="system_config_tabs"', $this->getResponse()->getBody());
    }
}
