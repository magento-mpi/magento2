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
class Magento_Adminhtml_Controller_Catalog_Product_Action_AttributeTest extends Magento_Backend_Utility_Controller
{
    /**
     * @covers Magento_Adminhtml_Controller_Catalog_Product_Action_Attribute::saveAction
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testSaveActionRedirectsSuccessfully()
    {
        /** @var $session Magento_Adminhtml_Model_Session */
        $session = Mage::getSingleton('Magento_Adminhtml_Model_Session');
        $session->setProductIds(array(1));

        $this->dispatch('backend/admin/catalog_product_action_attribute/save/store/0');

        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        $expectedUrl = Mage::getUrl('backend/admin/catalog_product/index');
        $isRedirectPresent = false;
        foreach ($this->getResponse()->getHeaders() as $header) {
            if ($header['name'] === 'Location' && strpos($header['value'], $expectedUrl) === 0) {
                $isRedirectPresent = true;
            }
        }
        $this->assertTrue($isRedirectPresent);
    }
}
