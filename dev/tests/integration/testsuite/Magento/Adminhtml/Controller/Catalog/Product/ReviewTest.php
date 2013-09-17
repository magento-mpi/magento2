<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Controller_Catalog_Product_ReviewTest extends Magento_Backend_Utility_Controller
{
    /**
     * @magentoDataFixture Magento/Review/_files/review_xss.php
     */
    public function testEditActionProductNameXss()
    {
        $reviewId = Mage::getModel('Magento_Review_Model_Review')->load(1, 'entity_pk_value')->getId();
        $this->dispatch('backend/admin/catalog_product_review/edit/id/' . $reviewId);
        $responseBody = $this->getResponse()->getBody();
        $this->assertContains('&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;', $responseBody);
        $this->assertNotContains('<script>alert("xss");</script>', $responseBody);
    }
}
