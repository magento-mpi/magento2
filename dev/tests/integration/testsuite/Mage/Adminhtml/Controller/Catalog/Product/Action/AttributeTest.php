<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Controller_Catalog_Product_Action_AttributeTest extends Mage_Backend_Utility_Controller
{
    /**
     * @covers Mage_Adminhtml_Controller_Catalog_Product_Action_Attribute::saveAction
     *
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testSaveActionRedirectsSuccessfully()
    {
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('Mage_Adminhtml_Model_Session');
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
