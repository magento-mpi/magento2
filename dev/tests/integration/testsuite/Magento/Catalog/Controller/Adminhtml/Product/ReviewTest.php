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
namespace Magento\Catalog\Controller\Adminhtml\Product;

/**
 * @magentoAppArea adminhtml
 */
class ReviewTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDataFixture Magento/Review/_files/review_xss.php
     */
    public function testEditActionProductNameXss()
    {
        $reviewId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Review\Model\Review'
        )->load(
            1,
            'entity_pk_value'
        )->getId();
        $this->dispatch('backend/review/product/edit/id/' . $reviewId);
        $responseBody = $this->getResponse()->getBody();
        $this->assertContains('&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;', $responseBody);
        $this->assertNotContains('<script>alert("xss");</script>', $responseBody);
    }
}
