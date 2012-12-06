<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Catalog_Product_ReviewControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @magentoDataFixture Mage/Review/_files/review_xss.php
     */
    public function testEditActionProductNameXss()
    {
        $this->markTestIncomplete('MAGETWO-5900');
        $this->dispatch('backend/admin/catalog_product_review/edit/id/1');
        $responseBody = $this->getResponse()->getBody();
        $this->assertContains('&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;', $responseBody);
        $this->assertNotContains('<script>alert("xss");</script>', $responseBody);
    }
}
