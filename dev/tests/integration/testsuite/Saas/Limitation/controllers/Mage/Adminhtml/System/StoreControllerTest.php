<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Mage_Adminhtml_System_StoreControllerTest extends Mage_Backend_Utility_Controller
{
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
}
