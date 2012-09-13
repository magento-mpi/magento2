<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Common class for product resource tests.
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */

abstract class Webapi_Catalog_Product_AdminAbstract extends Webapi_Catalog_Product_Abstract
{
    protected $_userType = 'admin';

    /**
     * Create product with API
     *
     * @param array $productData
     * @return int
     */
    protected function _createProductWithApi($productData)
    {
        $restResponse = $this->_tryToCreateProductWithApi($productData);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus(), 'Could not create product.');

        return $this->_getProductIdFromResponse($restResponse);
    }

    /**
     * Retrieve product ID from response Location header.
     *
     * @param Magento_Test_Webservice_Rest_ResponseDecorator $restResponse
     * @return int
     */
    protected function _getProductIdFromResponse($restResponse)
    {
        $location = $restResponse->getHeader('Location');
        list($productId) = array_reverse(explode('/', $location));
        return $productId;
    }

    /**
     * Try to create product with API request
     *
     * @param array $productData
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    protected function _tryToCreateProductWithApi($productData)
    {
        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        return $restResponse;
    }

    /**
     * Update product with API
     *
     * @param int $productId
     * @param array $productData
     * @param null|string $store
     * @return int
     */
    protected function _updateProductWithApi($productId, $productData, $store = null)
    {
        $restResponse = $this->_tryToUpdateProductWithApi($productId, $productData, $store);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus(), 'Could not update product.');

        return $restResponse;
    }

    /**
     * Try to update product with API request
     *
     * @param int $productId
     * @param array $productData
     * @param null|string $store
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    protected function _tryToUpdateProductWithApi($productId, $productData, $store = null)
    {
        $restResponse = $this->callPut($this->_getResourcePath($productId, $store), $productData);
        return $restResponse;
    }

    /**
     * Check if product data equals expected data
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $expectedData
     */
    protected function _checkProductData($product, $expectedData)
    {
        $this->assertNotNull($product->getId());
        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');
        foreach ($dateAttributes as $attribute) {
            if (isset($expectedData[$attribute])) {
                $this->assertEquals(strtotime($expectedData[$attribute]), strtotime($product->getData($attribute)),
                    $attribute . ' is not equal.');
            }
        }
        $exclude = array_merge($dateAttributes, array('group_price', 'tier_price', 'stock_data'));
        $productAttributes = array_diff_key($expectedData, array_flip($exclude));
        foreach ($productAttributes as $attribute => $value) {
            $this->assertEquals($value, $product->getData($attribute), $attribute . ' is not equal.');
        }
        if (isset($expectedData['stock_data'])) {
            $stockItem = $product->getStockItem();
            foreach ($expectedData['stock_data'] as $attribute => $value) {
                $this->assertEquals($value, $stockItem->getData($attribute), $attribute . ' is not equal.');
            }
        }
    }

    /**
     * Perform collection get with data check
     *
     * @param array $products
     * @param array $firstProductExpectedData
     * @param string $storeCode
     */
    protected function _checkProductCollectionGet($products, $firstProductExpectedData, $storeCode = null)
    {
        // find all products created after one with id = $firstProductId
        $requestParams = array(
            'filter[0][attribute]' => "entity_id",
            'filter[0][gteq][0]' => $firstProductExpectedData['entity_id'],
        );
        $restResponse = $this->callGet($this->_getResourcePath(null, $storeCode), $requestParams);
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());
        $resultProducts = $restResponse->getBody();
        $this->assertEquals(count($products), count($resultProducts), "Invalid products number in response");

        $isFirstProductFoundInResponse = false;
        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');

        // check only first product data
        foreach ($resultProducts as $resultProductData) {
            if ($resultProductData['entity_id'] == $firstProductExpectedData['entity_id']) {
                $isFirstProductFoundInResponse = true;
                foreach ($resultProductData as $key => $resultProductValue) {
                    if (in_array($key, $dateAttributes)) {
                        $this->assertEquals(strtotime($firstProductExpectedData[$key]), strtotime($resultProductValue),
                            "'$key' is invalid");
                    } else {
                        $this->assertEquals($firstProductExpectedData[$key], $resultProductValue, "'$key' is invalid");
                    }
                }
            }
        }
        $this->assertEquals(true, $isFirstProductFoundInResponse,
            "Product with id={$firstProductExpectedData['entity_id']} was not found in response");
    }

    /**
     * Check simple attributes values. Does not check attributes of complex types
     *
     * @param array $expectedData
     * @param array $actualData
     */
    protected function _checkSimpleAttributes($expectedData, $actualData)
    {
        foreach ($actualData as $field => $value) {
            if (!is_array($value)) {
                $this->assertEquals($expectedData[$field], $value, "The '$field' field has invalid value.");
            }
        }
    }

    /**
     * Mark product for removal in tear down
     *
     * @param $product
     */
    protected function _deleteProductAfterTest($product)
    {
        $this->addModelToDelete($product, true);
    }
}

