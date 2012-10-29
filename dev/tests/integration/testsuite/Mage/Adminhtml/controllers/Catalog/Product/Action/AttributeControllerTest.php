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
     * @ group module:Mage_Adminhtml
     */
class Mage_Adminhtml_Catalog_Product_Action_AttributeControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @covers Mage_Adminhtml_Catalog_Product_Action_AttributeController::saveAction
     *
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testSaveActionSaveButton123()
    {
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('Mage_Adminhtml_Model_Session');
        $session->setProductIds(array(1));
        $this->dispatch('backend/admin/catalog_product_action_attribute/save/store/0');
        $responseCode = $this->getResponse()->getHttpResponseCode();
        $headers = $this->getResponse()->getHeaders();
        $isRedirectPresent = false;
        $this->assertEquals('302', $responseCode);
        $expectedUrl = Mage::getUrl('backend/admin/catalog_product/index');
        foreach ($headers as $header){
            if (strpos($headers, $expectedUrl) !==false && $header['name']==='Location'){
                $isRedirectPresent = true;
            }
        }
        $this->assertTrue($isRedirectPresent, 'message');
    }
}