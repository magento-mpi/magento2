<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_System_StoreControllerTest extends Mage_Backend_Utility_Controller
{
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/system_store/index');

        $response = $this->getResponse()->getBody();
        $this->assertContains('Create Website', $response);
        $this->assertContains('Create Store View', $response);
    }

    /**
     * @magentoConfigFixture limitations/website 1
     */
    public function testIndexActionWebsiteRestricted()
    {
        $this->dispatch('backend/admin/system_store/index');
        $response = $this->getResponse()->getBody();
        $this->assertContains('Create Website', $response);
        $this->assertContains('You are using the maximum number of Websites allowed.', $response);
    }

    /**
     * @magentoConfigFixture limitations/store 1
     * @magentoConfigFixture limitations/store_group 1
     */
    public function testIndexActionStoreRestricted()
    {
        $this->dispatch('backend/admin/system_store/index');
        $response = $this->getResponse()->getBody();
        $this->assertNotContains('>Create Store View<', $response);
        $this->assertNotContains('>Create Store<', $response);
        $this->assertContains('You are using the maximum number of store views allowed.', $response);
        $this->assertContains('You are using the maximum number of stores allowed.', $response);
    }
}
