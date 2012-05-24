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
 * Test for configurable associated products resource (guest role)
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Products_Configurable_AssociatedProduct_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    /**
     * Test that list of products assigned to configurable product is forbidden for guest
     *
     * @resourceOperation product_configurable_associated_product::multiget
     */
    public function testList()
    {
        $restResponse = $this->callGet($this->_getResourcePath('configurable_id'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test that product assign to configurable product is forbidden for guest
     *
     * @resourceOperation product_configurable_associated_product::create
     */
    public function testPost()
    {
        $restResponse = $this->callPost($this->_getResourcePath('configurable_id'), array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test that product unassign from configurable product is forbidden for guest
     *
     * @resourceOperation product_configurable_associated_product::delete
     */
    public function testDelete()
    {
        $restResponse = $this->callDelete($this->_getResourcePath('configurable_id', 'simple_id'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Create path to resource
     *
     * @param string $configurableProductId
     * @param null|string $associatedProductId
     * @param null|string $storeId
     * @return string
     */
    protected function _getResourcePath($configurableProductId, $associatedProductId = null, $storeId = null)
    {
        $path = "products/$configurableProductId/configurable_associated_products";
        if (!is_null($associatedProductId)) {
            $path .= "/$associatedProductId";
        }
        if (!is_null($storeId)) {
            $path .= "/store/$storeId";
        }
        return $path;
    }
}
