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
class Saas_Launcher_Adminhtml_Storelauncher_IndexControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoConfigFixture limitations/catalog_product 1
     */
    public function testIndexAction()
    {
        $this->dispatch('backend/launcher/storelauncher_index/index');
        $body = $this->getResponse()->getBody();
        $this->assertSelectRegExp('button.disabled span', '/Add Products/', 1, $body);
    }
}
