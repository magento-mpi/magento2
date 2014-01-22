<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Helper;

class DataTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var Data
     */
    private $wishlistHelper;

    /**
     * @var \Magento\ObjectManager
     */
    private $objectManager;

    /**
     * Get requrer instance
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->wishlistHelper = $this->objectManager->get('Magento\Wishlist\Helper\Data');
    }

    /**
     * Clear wishlist helper property
     */
    protected function tearDown()
    {
        $this->_wishlistHelper = null;
    }

    public function testGetAddParams()
    {
        $product = $this->objectManager->get('Magento\Catalog\Model\Product');
        $product->setId(11);
        $json = $this->wishlistHelper->getAddParams($product);
        $params = (array) json_decode($json);
        $data = (array) $params['data'];
        $this->assertEquals('11', $data['product']);
        $this->assertArrayHasKey('form_key', $data);
        $this->assertArrayHasKey('uenc', $data);
        $this->assertStringEndsWith(
            'wishlist/index/add/',
            $params['action']
        );
    }

    public function testGetMoveFromCartParams()
    {
        $json = $this->wishlistHelper->getMoveFromCartParams(11);
        $params = (array) json_decode($json);
        $data = (array) $params['data'];
        $this->assertEquals('11', $data['item']);
        $this->assertArrayHasKey('form_key', $data);
        $this->assertArrayHasKey('uenc', $data);
        $this->assertStringEndsWith(
            'wishlist/index/fromcart/',
            $params['action']
        );
    }

    public function testGetUpdateParams()
    {
        $product = $this->objectManager->get('Magento\Catalog\Model\Product');
        $product->setId(11);
        $product->setWishlistItemId(15);
        $json = $this->wishlistHelper->getUpdateParams($product);
        $params = (array) json_decode($json);
        $data = (array) $params['data'];
        $this->assertEquals('11', $data['product']);
        $this->assertEquals('15', $data['id']);
        $this->assertArrayHasKey('form_key', $data);
        $this->assertArrayHasKey('uenc', $data);
        $this->assertStringEndsWith(
            'wishlist/index/updateItemOptions/',
            $params['action']
        );
    }

}
