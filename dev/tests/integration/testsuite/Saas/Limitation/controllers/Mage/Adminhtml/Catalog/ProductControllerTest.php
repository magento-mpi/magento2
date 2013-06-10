<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Mage_Adminhtml_Catalog_ProductControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * Return the expected message, used by product limitation
     *
     * @return string
     */
    protected function _getCreateRestrictedMessage()
    {
        /** @var Saas_Limitation_Model_Catalog_Product_Limitation $limitation */
        $limitation = Mage::getModel('Saas_Limitation_Model_Catalog_Product_Limitation');
        return $limitation->getCreateRestrictedMessage();
    }

    /**
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testIndexActionRestricted()
    {
        $this->dispatch('backend/admin/catalog_product');
        $body = $this->getResponse()->getBody();

        $this->assertContains($this->_getCreateRestrictedMessage(), $body);

        $this->assertSelectCount('#add_new_product', 1, $body,
            '"Add Product" button container should be present on Manage Products page, if the limit is reached');
        $this->assertSelectCount('#add_new_product-button.disabled', 1, $body,
            '"Add Product" button should be present and disabled on Manage Products page, if the limit is reached');
        $this->assertSelectCount('#add_new_product .action-toggle', 0, $body,
            '"Add Product" button split should not be present on Manage Products page, if the limit is reached');
    }

    /**
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoConfigFixture limitations/catalog_category 1
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testEditActionRestricted()
    {
        $this->dispatch('backend/admin/catalog_product/edit/id/1');
        $body = $this->getResponse()->getBody();
        $this->assertContains($this->_getCreateRestrictedMessage(), $body);
        $this->assertSelectCount('#save-split-button', 1, $body,
            '"Save" button isn\'t present on Edit Product page');
        $this->assertSelectCount('#save-split-button-new-button', 0, $body,
            '"Save & New" button should not be present on Edit Product page, if the limit is reached');
        $this->assertSelectCount('#save-split-button-duplicate-button', 0, $body,
            '"Save & Duplicate" should not be present on Edit Product page, if the limit is reached');
        $this->assertContains('Sorry, you are using all the categories your account allows.'
            . ' To add more, first delete a category or upgrade your service.', $body,
            'New category creation should be restricted on Edit Product page, if the limit is reached');
        $pattern = '/<button[^>]*New\sCategory[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnRoot = $matches[0];
        $this->assertContains('disabled="disabled"', $btnRoot,
            '"New Category" button should be disabled on New Product page, if the limit is reached');
    }

    /**
     * @magentoConfigFixture limitations/catalog_product 2
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testEditActionAllowedNewProduct()
    {
        $this->dispatch('backend/admin/catalog_product/edit/id/1');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('#save-split-button', 1, $body,
            '"Save" button isn\'t present on Edit Product page');
        $this->assertSelectCount('#save-split-button-new-button', 1, $body,
            '"Save & New" button isn\'t present on Edit Product page');
        $this->assertSelectCount('#save-split-button-duplicate-button', 1, $body,
            '"Save & Duplicate" isn\'t present on Edit Product page');
    }

    /**
     * @magentoConfigFixture limitations/catalog_product 2
     */
    public function testNewActionAllowedNewProduct()
    {
        /** @var $installer Mage_Catalog_Model_Resource_Setup */
        $installer = Mage::getResourceModel(
            'Mage_Catalog_Model_Resource_Setup',
            array('resourceName' => 'catalog_setup')
        );
        $attributeSetId = $installer->getDefaultAttributeSetId('catalog_product');

        $this->dispatch("backend/admin/catalog_product/new/set/$attributeSetId/type/simple");
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('#save-split-button', 1, $body,
            '"Save" button isn\'t present on New Product page');
        $this->assertSelectCount('#save-split-button-new-button', 1, $body,
            '"Save & New" button isn\'t present on New Product page');
        $this->assertSelectCount('#save-split-button-duplicate-button', 1, $body,
            '"Save & Duplicate" button isn\'t present on New Product page');
    }

    /**
     * @magentoConfigFixture limitations/catalog_product 1
     */
    public function testNewActionRestrictedNewProduct()
    {
        $this->dispatch('backend/admin/catalog_product/new/set/4/type/simple');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('#save-split-button', 1, $body,
            '"Save" button isn\'t present on New Product page');
        $this->assertSelectCount('#save-split-button-new-button', 0, $body,
            '"Save & New" button should not be present on New Product page, if last allowed product is being created');
        $this->assertSelectCount('#save-split-button-duplicate-button', 0, $body,
            '"Save & Duplicate" should not be present on New Product page, if last allowed product is being created');
    }
}
