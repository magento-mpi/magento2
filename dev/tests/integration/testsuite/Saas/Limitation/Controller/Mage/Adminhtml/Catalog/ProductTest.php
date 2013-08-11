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
class Saas_Limitation_Magento_Adminhtml_Controller_Catalog_ProductTest extends Mage_Backend_Utility_Controller
{
    /**
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testIndexActionRestricted()
    {
        $this->dispatch('backend/admin/catalog_product');
        $body = $this->getResponse()->getBody();

        $this->assertContains('Sorry, you are using all the products and variations your account allows.', $body);

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
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testEditActionRestricted()
    {
        $this->dispatch('backend/admin/catalog_product/edit/id/1');
        $body = $this->getResponse()->getBody();
        $this->assertContains('Sorry, you are using all the products and variations your account allows.', $body);
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
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
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
        /** @var $installer Magento_Catalog_Model_Resource_Setup */
        $installer = Mage::getResourceModel(
            'Magento_Catalog_Model_Resource_Setup',
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

    /**
     * @magentoConfigFixture limitations/catalog_product 2
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @dataProvider validateActionOnVariationsLimitReachedDataProvider
     */
    public function testValidateActionOnVariationsLimitReached($productId, $expectedMessage)
    {
        $productData = array();
        $variationsMatrix = array(
            array('configurable_attribute' => '{"size":1}'),
            array('configurable_attribute' => '{"size":1}'),
        );
        $this->getRequest()
            ->setPost('product', $productData)
            ->setPost('id', $productId)
            ->setPost('variations-matrix', $variationsMatrix);

        $this->dispatch('backend/admin/catalog_product/validate');

        $this->assertContains($expectedMessage, $this->getResponse()->getBody());
    }

    public static function validateActionOnVariationsLimitReachedDataProvider()
    {
        $message = 'You tried to add %d products, but the most you can have is %d.';
        return array(
            'new product' => array(
                null,
                sprintf($message, 3, 2),
            ),
            'existing product' => array(
                1,
                sprintf($message, 2, 2),
            ),
        );
    }

    /**
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testSaveActionAndDuplicateLimitationReached()
    {
        $this->getRequest()->setPost(array('back' => 'duplicate'));
        $this->dispatch('backend/admin/catalog_product/save/id/1');
        $this->assertRedirect(
            $this->stringStartsWith('http://localhost/index.php/backend/admin/catalog_product/edit/id/1')
        );
        $this->assertSessionMessages(
            $this->contains('You saved the product.'), Magento_Core_Model_Message::SUCCESS
        );
        $this->assertSessionMessages($this->contains("You can't create new product."), Magento_Core_Model_Message::ERROR);
    }

    /**
     * @magentoConfigFixture limitations/catalog_product 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testSaveActionAndNewLimitationReached()
    {
        $this->getRequest()->setPost(array('back' => 'new'));
        $this->dispatch('backend/admin/catalog_product/save/id/1');
        $this->assertRedirect(
            $this->stringStartsWith('http://localhost/index.php/backend/admin/catalog_product/edit/id/1')
        );
        $this->assertSessionMessages(
            $this->contains('You saved the product.'), Magento_Core_Model_Message::SUCCESS
        );
        $this->assertSessionMessages($this->contains("You can't create new product."), Magento_Core_Model_Message::ERROR);
    }

    /**
     * @magentoConfigFixture limitations/catalog_category 2
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testEditActionAllowedNewCategory()
    {
        $this->dispatch('backend/admin/catalog_product/edit/id/1');
        $body = $this->getResponse()->getBody();
        $this->assertNotContains('Sorry, you are using all the categories your account allows.'
            . ' To add more, first delete a category or upgrade your service.', $body,
            'New category creation should not be restricted on Edit Product page');
        $pattern = '/<button[^>]*New\sCategory[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnRoot = $matches[0];
        $this->assertNotContains('disabled="disabled"', $btnRoot,
            '"New Category" button should be enabled on New Product page, if the limit is not reached');
    }
}
