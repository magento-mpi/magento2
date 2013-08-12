<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Controller_System_StoreTest extends Magento_Backend_Utility_Controller
{
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/system_store/index');

        $response = $this->getResponse()->getBody();

        $this->assertSelectEquals('#add', 'Create Website', 1, $response);
        $this->assertSelectCount('#add_group', 1, $response);
        $this->assertSelectCount('#add_store', 1, $response);

        $this->assertSelectEquals('#add.disabled', 'Create Website', 0, $response);
        $this->assertSelectCount('#add_group.disabled', 0, $response);
        $this->assertSelectCount('#add_store.disabled', 0, $response);
    }
}
