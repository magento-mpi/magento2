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
namespace Magento\Backend\Controller\Adminhtml\System;

class ConfigTest extends \Magento\Backend\Utility\Controller
{
    public function testEditAction()
    {
        $this->dispatch('backend/admin/system_config/edit');
        $this->assertContains('<ul id="system_config_tabs"', $this->getResponse()->getBody());
    }
}
