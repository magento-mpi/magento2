<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Mage_Connect_Controller_Adminhtml_Extension_Local
 *
 * @magentoAppArea adminhtml
 */
class Mage_Connect_Controller_Adminhtml_Extension_LocalTest extends Mage_Backend_Utility_Controller
{
    public function testIndexAction()
    {
        $expected = '?return=' . urlencode(Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl());
        $this->dispatch('backend/admin/extension_local/index');
        $this->assertRedirect($this->stringEndsWith($expected));
    }
}
