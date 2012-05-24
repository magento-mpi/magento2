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
 * Test category products resource for guest
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Category_Product_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    /**
     * Test unsuccessful get. Get is not implemented
     *
     * @resourceOperation category_product::get
     */
    public function testGet()
    {
        $restResponse = $this->callGet($this->_getResourcePath(Mage_Catalog_Model_Category::TREE_ROOT_ID, 'product'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test that list of products assigned to category is forbidden for guest
     *
     * @resourceOperation category_product::multiget
     */
    public function testList()
    {
        $categoryId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $restResponse = $this->callGet($this->_getResourcePath($categoryId));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test that product assign to category is forbidden for guest
     *
     * @resourceOperation category_product::create
     */
    public function testPost()
    {
        $categoryId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $restResponse = $this->callPost($this->_getResourcePath($categoryId), array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test that product assigned to category change is forbidden for guest
     *
     * @resourceOperation category_product::update
     */
    public function testPut()
    {
        $categoryId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $restResponse = $this->callPut($this->_getResourcePath($categoryId, 'product_id'), array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test that product unassign from category is forbidden for guest
     *
     * @resourceOperation category_product::delete
     */
    public function testDelete()
    {
        $categoryId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $restResponse = $this->callDelete($this->_getResourcePath($categoryId, 'product_id'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Create path to resource
     *
     * @param string $categoryId
     * @param null|string $productId
     * @param null|string $storeId
     * @return string
     */
    protected function _getResourcePath($categoryId, $productId = null, $storeId = null)
    {
        $path = "categories/$categoryId/products";
        if (!is_null($productId)) {
            $path .= "/$productId";
        }
        if (!is_null($storeId)) {
            $path .= "/store/$storeId";
        }
        return $path;
    }
}
