<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test \Magento\Connect\Controller\Adminhtml\Extension\Local
 *
 * @magentoAppArea adminhtml
 */
class Magento_Connect_Controller_Adminhtml_Extension_LocalTest extends Magento_Backend_Utility_Controller
{
    public function testIndexAction()
    {
        $expected = '?return=' . urlencode(Mage::helper('Magento\Backend\Helper\Data')->getHomePageUrl());
        $this->dispatch('backend/admin/extension_local/index');
        $this->assertRedirect($this->stringEndsWith($expected));
    }
}
