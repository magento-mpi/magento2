<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdminGws\Magento\CatalogEvent\Controller\Adminhtml\Catalog;

/**
 * Test that CatalogEvent controller is wrapped by AdminGws
 *
 * @magentoAppArea adminhtml
 * @magentoDataFixture Magento/AdminGws/_files/role_websites_login.php
 */
class EventTest extends \Magento\Backend\Utility\Controller
{
    /**
     * Get credentials to login restricted admin user
     *
     * @return array
     */
    protected function _getAdminCredentials()
    {
        return array(
            'user' => 'admingws_user',
            'password' => 'admingws_password1',
        );
    }

    public function testIndexActionRestrictedUserCanSeeGrid()
    {
        $this->dispatch('backend/admin/catalog_event/index/');
        $body = $this->getResponse()->getBody();

        $this->assertContains('Events', $body);
        $this->assertTag(array('tag' => 'table', 'id' => 'catalogEventGrid_table'), $body,
            'Events grid is not found');
    }
}
