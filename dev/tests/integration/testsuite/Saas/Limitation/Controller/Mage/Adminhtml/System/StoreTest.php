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
class Saas_Limitation_Mage_Adminhtml_Controller_System_StoreTest extends Mage_Backend_Utility_Controller
{
    /**
     * @magentoConfigFixture limitations/website 1
     */
    public function testIndexActionWebsiteRestricted()
    {
        $this->dispatch('backend/admin/system_store/index');
        $response = $this->getResponse()->getBody();
        $this->assertNotContains('Sorry, but you can\'t add any more websites with this account.', $response);
        $this->assertSelectRegExp('#add', '/Create Website/', 0, $response);
    }

    /**
     * @magentoConfigFixture limitations/store 1
     */
    public function testIndexActionStoreRestricted()
    {
        $this->dispatch('backend/admin/system_store/index');
        $response = $this->getResponse()->getBody();
        $this->assertSelectCount('#add_store.disabled', 1, $response);
        $this->assertContains('Sorry, you are using all the store views your account allows. '
            . 'To add more, first delete a store view or upgrade your service.', $response);
    }

    /**
     * @magentoConfigFixture limitations/store_group 1
     */
    public function testIndexActionStoreGroupRestricted()
    {
        $this->dispatch('backend/admin/system_store/index');
        $response = $this->getResponse()->getBody();
        $this->assertSelectCount('#add_group', 0, $response);
        $this->assertNotContains('You are using the maximum number of stores allowed.', $response);
    }
}
