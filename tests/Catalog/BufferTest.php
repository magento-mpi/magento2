<?php

class Catalog_Test extends Mage_Selenium_TestCase
{
/**
     *
     * <p>Creating products for testing.</p>
     *
     * <p>Navigate to Sales-Orders page.</p>
     *
     */
    protected function assertPreConditions()
    {
        $this->addParameter('tabName', '');
        $this->addParameter('webSite', '');
        $this->addParameter('storeName', '');
    }

    /**
     * <p>Creating Simple product with required fields only</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function createSimple()
    {
        //Data
        $productData = $this->loadData('simple_product_for_order_fullfill', NULL, array('general_name', 'general_sku'));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        return $productData['general_name'];
    }

    /**
     * @depends createSimple
     * @test
     */
    public function navigateToProductPage($productData)
    {
        $product = $this->loadData('product_data_template', array ('product_name' => $productData));
        $this->logoutCustomer();
        $this->catalogHelper()->validateProduct($product);
    }
}
