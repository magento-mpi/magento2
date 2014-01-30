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


namespace Magento\Catalog\Controller\Adminhtml\Product\Action;

/**
 * @magentoAppArea adminhtml
 */
class AttributeTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @covers \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute::saveAction
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testSaveActionRedirectsSuccessfully()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $session \Magento\Backend\Model\Session */
        $session = $objectManager->get('Magento\Backend\Model\Session');
        $session->setProductIds(array(1));

        $this->dispatch('backend/catalog/product_action_attribute/save/store/0');

        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        /** @var \Magento\Backend\Model\UrlInterface $urlBuilder */
        $urlBuilder = $objectManager->get('Magento\UrlInterface');

        /** @var \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper */
        $attributeHelper = $objectManager->get('Magento\Catalog\Helper\Product\Edit\Action\Attribute');
        $expectedUrl = $urlBuilder->getUrl(
            'catalog/product/index', array('store' => $attributeHelper->getSelectedStoreId()))
        ;
        $isRedirectPresent = false;
        foreach ($this->getResponse()->getHeaders() as $header) {
            if ($header['name'] === 'Location' && strpos($header['value'], $expectedUrl) === 0) {
                $isRedirectPresent = true;
            }
        }

        $this->assertTrue($isRedirectPresent);
    }
}
