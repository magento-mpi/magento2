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

namespace Magento\Adminhtml\Controller\Catalog\Product\Action;

/**
 * @magentoAppArea adminhtml
 */
class AttributeTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @covers \Magento\Adminhtml\Controller\Catalog\Product\Action\Attribute::saveAction
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testSaveActionRedirectsSuccessfully()
    {
        /** @var $session \Magento\Adminhtml\Model\Session */
        $session = \Mage::getSingleton('Magento\Adminhtml\Model\Session');
        $session->setProductIds(array(1));

        $this->dispatch('backend/admin/catalog_product_action_attribute/save/store/0');

        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        $expectedUrl = \Mage::getUrl('backend/admin/catalog_product/index');
        $isRedirectPresent = false;
        foreach ($this->getResponse()->getHeaders() as $header) {
            if ($header['name'] === 'Location' && strpos($header['value'], $expectedUrl) === 0) {
                $isRedirectPresent = true;
            }
        }
        $this->assertTrue($isRedirectPresent);
    }
}
