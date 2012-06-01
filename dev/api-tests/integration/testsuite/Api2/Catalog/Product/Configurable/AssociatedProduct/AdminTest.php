<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for configurable associated products resource (admin role)
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Product_Configurable_AssociatedProduct_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Test successful assigned products list with data
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable_with_assigned_products.php
     * @resourceOperation configurable_associated_product::multiget
     */
    public function testMultiGet()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        $this->_testMultiGet($configurable);
    }

    /**
     * Test unsuccessful assigned products list get from non-configurable product
     *
     * @magentoDataFixture Catalog/Product/Simple/product_simple.php
     * @resourceOperation configurable_associated_product::multiget
     */
    public function testMultiGetFromProductOfInvalidType()
    {
        /** @var $simple Mage_Catalog_Model_Product */
        $simple = $this->getFixture('product_simple');
        $restResponse = $this->callGet($this->_getResourcePath($simple->getId()));
        $expectedMessage = 'Only configurable products can be used for retrieving the list of assigned products.';
        $this->_checkErrorMessagesInResponse($restResponse, $expectedMessage);
    }

    /**
     * Test unsuccessful assigned products list get
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @resourceOperation configurable_associated_product::multiget
     */
    public function testMultiGetWithoutAssignedProducts()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        $this->_testMultiGet($configurable);
    }

    /**
     * Test list of associated products with invalid configurable product ID specified
     *
     * @resourceOperation configurable_associated_product::multiget
     */
    public function testMultiGetInvalidConfigurable()
    {
        $restResponse = $this->callGet($this->_getResourcePath('invalid_id'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus(),
            "Response code is invalid.");
    }

    /**
     * Test successful simple products assign to configurable product
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @magentoDataFixture Catalog/Product/Configurable/product_simple_with_configurable_attribute_set.php
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreate()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $associatedData = array('product_id' => $product->getId());
        $this->_assignSuccessful($configurable, $associatedData);
    }

    /**
     * Test successful simple products assign to configurable product. Associated product SKU is used
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @magentoDataFixture Catalog/Product/Configurable/product_simple_with_configurable_attribute_set.php
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreateUsingAssociatedSku()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $associatedData = array('product_id' => $product->getSku());
        $this->_assignSuccessful($configurable, $associatedData);
    }

    /**
     * Test successful simple products assign to configurable product
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @magentoDataFixture Catalog/Product/Configurable/product_downloadable_with_configurable_attribute_set.php
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreateWithDownloadableAssigned()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_downloadable');
        $associatedData = array('product_id' => $product->getId());
        $this->_assignSuccessful($configurable, $associatedData);
    }

    /**
     * Test unsuccessful giftcard assign to configurable product
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @magentoDataFixture Catalog/Product/Configurable/product_giftcard_with_configurable_attribute_set.php
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreateInvalidAssociatedType()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $giftcard Mage_Catalog_Model_Product */
        $giftcard = $this->getFixture('product_giftcard');
        $associatedData = array('product_id' => $giftcard->getId());
        $restResponse = $this->callPost($this->_getResourcePath($configurable->getId()), $associatedData);
        $expectedMessage = sprintf('The product of the "%s" type cannot be assigned to the configurable product.',
            $giftcard->getTypeId());
        $this->_checkErrorMessagesInResponse($restResponse, $expectedMessage);
    }

    /**
     * Test unsuccessful product with non-unique options set assign to the configurable product
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @magentoDataFixture Catalog/Product/Configurable/product_simple_with_configurable_attribute_set.php
     * @magentoDataFixture Catalog/Product/Configurable/product_downloadable_with_configurable_attribute_set.php
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreateWithNonUniqueOptions()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $simple Mage_Catalog_Model_Product */
        $simple = $this->getFixture('product_simple');
        $configurable->setConfigurableProductsData(array($simple->getId() => $simple->getId()))->save();
        /** @var $downloadable Mage_Catalog_Model_Product */
        $downloadable = $this->getFixture('product_downloadable');
        $associatedData = array('product_id' => $downloadable->getId());
        $restResponse = $this->callPost($this->_getResourcePath($configurable->getId()), $associatedData);
        $expectedMessage = 'A product with the same configurable attributes\' values is already assigned '
            . 'to the configurable one.';
        $this->_checkErrorMessagesInResponse($restResponse, $expectedMessage);
    }

    /**
     * Test unsuccessful simple product associate to the configurable.
     * Simple and configurable products are from different attribute sets
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @magentoDataFixture Catalog/Product/Simple/product_simple.php
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreateWithInvalidAttributeSet()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $associated Mage_Catalog_Model_Product */
        $associated = $this->getFixture('product_simple');
        $associatedData = array('product_id' => $associated->getId());
        $restResponse = $this->callPost($this->_getResourcePath($configurable->getId()), $associatedData);
        $expectedMessage = "The product to be associated must have the same attribute set as the configurable one.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedMessage);
    }

    /**
     * Test unsuccessful product assign to the configurable one if the association was created earlier
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @magentoDataFixture Catalog/Product/Configurable/product_simple_with_configurable_attribute_set.php
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreateProductAlreadyAssigned()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $associated Mage_Catalog_Model_Product */
        $associated = $this->getFixture('product_simple');
        $configurable->setConfigurableProductsData(array($associated->getId() => $associated->getId()))->save();
        $associatedData = array('product_id' => $associated->getId());
        $restResponse = $this->callPost($this->_getResourcePath($configurable->getId()), $associatedData);
        $expectedMessage = "The product to be assigned is already assigned to the specified configurable one.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedMessage);
    }

    /**
     * Test unsuccessful associate products to the configurable one. Invalid configurable product ID specified
     *
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreateInvalidConfigurableProductId()
    {
        $associated = array('product_id' => 1);
        $restResponse = $this->callPost($this->_getResourcePath('invalid_id'), $associated);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus(),
            "Response code is invalid.");
    }

    /**
     * Test unsuccessful associate products to the configurable one. Invalid associated product ID specified
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreateInvalidAssociatedProductId()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        $associated = array('product_id' => 'invalid_id');
        $restResponse = $this->callPost($this->_getResourcePath($configurable->getId()), $associated);
        $expectedErrorMessage = "ID of the product to be assigned is invalid or product with such ID does not exist.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test unsuccessful associate products to the configurable one. Associated product ID is not specified
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreateAssociatedProductIdNotSpecified()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        $associated = array('some_field' => 1);
        $restResponse = $this->callPost($this->_getResourcePath($configurable->getId()), $associated);
        $expectedErrorMessage = 'ID of the product to be associated must be specified.';
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test unsuccessful product without configurable option value assign to configurable product
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @magentoDataFixture Catalog/Product/Configurable/product_simple_without_configurable_attribute.php
     * @resourceOperation configurable_associated_product::create
     */
    public function testCreateNoConfigurableAttributeSpecified()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $associatedData = array('product_id' => $product->getId());
        $restResponse = $this->callPost($this->_getResourcePath($configurable->getId()), $associatedData);
        $expectedMessage = 'The product to be associated must have all configurable attribute values'
            . ' as the configurable product has.';
        $this->_checkErrorMessagesInResponse($restResponse, $expectedMessage);
    }

    /**
     * Test successful associated product unassign from the configurable one
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable_with_assigned_products.php
     * @resourceOperation configurable_associated_product::delete
     */
    public function testDelete()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $configurable->getTypeInstance();
        $associatedIds = $configurableType->getUsedProductIds($configurable);
        $associatedToBeUnassigned = reset($associatedIds);
        $restResponse = $this->callDelete($this->_getResourcePath($configurable->getId(), $associatedToBeUnassigned));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        /** @var $updatedConfigurable Mage_Catalog_Model_Product */
        $updatedConfigurable = Mage::getModel('Mage_Catalog_Model_Product')->load($configurable->getId());
        $configurableType = $updatedConfigurable->getTypeInstance();
        $realAssignedProducts = $configurableType->getUsedProductIds($updatedConfigurable);
        // remove element by value not by its key
        $associatedIds = array_flip($associatedIds);
        unset($associatedIds[$associatedToBeUnassigned]);
        $associatedIds = array_flip($associatedIds);

        $this->_checkAssignedProducts($associatedIds, $realAssignedProducts);
    }

    /**
     * Test unsuccessful product unassign from non-configurable product
     *
     * @magentoDataFixture Catalog/Product/Simple/product_simple.php
     * @resourceOperation configurable_associated_product::delete
     */
    public function testDeleteFromProductOfInvalidType()
    {
        /** @var $simple Mage_Catalog_Model_Product */
        $simple = $this->getFixture('product_simple');
        $restResponse = $this->callDelete($this->_getResourcePath($simple->getId(), 1));
        $expectedMessage = 'Only configurable products can be used to unassign an associated product from.';
        $this->_checkErrorMessagesInResponse($restResponse, $expectedMessage);
    }

    /**
     * Test unsuccessful product unassign from the configurable one. Invalid configurable product ID specified
     *
     * @magentoDataFixture Catalog/Product/Simple/product_simple.php
     * @resourceOperation configurable_associated_product::delete
     */
    public function testDeleteInvalidConfigurableProductId()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $restResponse = $this->callDelete($this->_getResourcePath('invalid_id', $product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus(),
            "Response code is invalid.");
    }

    /**
     * Test unsuccessful product unassign from the configurable one. Invalid associated product ID specified
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @resourceOperation configurable_associated_product::delete
     */
    public function testDeleteInvalidAssociatedProductId()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        $restResponse = $this->callDelete($this->_getResourcePath($configurable->getId(), 'invalid_id'));
        $expectedErrorMessage = "ID of the product to be unassigned is invalid or product with such ID does not exist.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test unsuccessful product unassign from the configurable one.
     * The specified product is not assigned to the configurable one
     *
     * @magentoDataFixture Catalog/Product/Configurable/configurable.php
     * @magentoDataFixture Catalog/Product/Simple/product_simple.php
     * @resourceOperation configurable_associated_product::delete
     */
    public function testDeleteUnassignedProduct()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $unassignedProduct Mage_Catalog_Model_Product */
        $unassignedProduct = $this->getFixture('product_simple');
        $restResponse = $this->callDelete($this->_getResourcePath($configurable->getId(), $unassignedProduct->getId()));
        $expectedErrorMessage = "The specified product cannot be unassigned from the configurable one "
            . "as it is not assigned to it.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test successful get of assigned to category products list
     *
     * @param Mage_Catalog_Model_Product $configurable
     */
    protected function _testMultiGet(Mage_Catalog_Model_Product $configurable)
    {
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $configurable->getTypeInstance();
        $expectedIds = $configurableType->getUsedProductIds($configurable);

        $restResponse = $this->callGet($this->_getResourcePath($configurable->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $assignedProductsFromResponse = $restResponse->getBody();
        $assignedIds = array();
        foreach($assignedProductsFromResponse as $assignedProduct) {
            $this->assertInternalType('array', $assignedProduct, 'Response has invalid format.');
            $this->assertArrayHasKey('product_id', $assignedProduct, 'Response has invalid format');
            $assignedIds[] = $assignedProduct['product_id'];
        }
        $this->_checkAssignedProducts($expectedIds, $assignedIds);
    }

    /**
     * Assign $associated product to the $configurable one. Check created associacion
     *
     * @param Mage_Catalog_Model_Product $configurable
     * @param array $associatedData
     */
    protected function _assignSuccessful($configurable, $associatedData)
    {
        $restResponse = $this->callPost($this->_getResourcePath($configurable->getId()), $associatedData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus(),
            "Invalid response code received.");
        /** @var $updatedConfigurable Mage_Catalog_Model_Product */
        $updatedConfigurable = Mage::getModel('Mage_Catalog_Model_Product')->load($configurable->getId());
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $updatedConfigurable->getTypeInstance();
        $realAssignedProducts = $configurableType->getUsedProductIds($updatedConfigurable);
        if (!is_numeric($associatedData['product_id'])) {
            /** @var $productHelper Mage_Catalog_Helper_Product */
            $productHelper = Mage::helper('Mage_Catalog_Helper_Product');
            $associatedProduct = $productHelper->getProduct($associatedData['product_id'],
                Mage::app()->getStore()->getId());
            $associatedData['product_id'] = $associatedProduct->getId();
        }
        $this->_checkAssignedProducts($associatedData, $realAssignedProducts);
    }

    /**
     * Check if assigned products are correct. Parameters could be passed in two formats
     *
     * @param array $expectedIds
     * @param array $assignedIds
     */
    protected function _checkAssignedProducts($expectedIds, $assignedIds)
    {
        $this->assertCount(count($expectedIds), $assignedIds,
            "Products quantity assigned to the configurable product is invalid.");
        foreach ($expectedIds as $expectedId) {
            $this->assertTrue(in_array($expectedId, $assignedIds), "Product with ID $expectedId not found.");
        }
    }

    /**
     * Create path to resource
     *
     * @param string $configurableProductId
     * @param null|string $associatedProductId
     * @return string
     */
    protected function _getResourcePath($configurableProductId, $associatedProductId = null)
    {
        $path = "products/$configurableProductId/configurable_associated_products";
        if (!is_null($associatedProductId)) {
            $path .= "/$associatedProductId";
        }
        return $path;
    }
}
