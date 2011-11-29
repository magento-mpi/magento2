<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test API login method
 *
 */
class Checkout_CartTest extends Magento_Test_Webservice
{
    /**
     * Product for test
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Quote object
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        // create product for test
        $this->_product = new Mage_Catalog_Model_Product;

        $this->_product->setData(array(
            'sku'               => 'simple' . uniqid(),
            'attribute_set_id'  => 4,
            'type_id'           => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
            'name'              => 'Simple Product',
            'website_ids'       => array(Mage::app()->getStore()->getWebsiteId()),
            'description'       => '...',
            'short_description' => '...',
            'price'             => 0.99,
            'tax_class_id'      => 2,
            'visibility'        => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            'status'            => Mage_Catalog_Model_Product_Status::STATUS_ENABLED
        ));
        $this->_product->save();

        // Disable exceptions to avoid errors in cookie processing
        Mage::$headersSentThrowsException = false;

        // create quote for test
        $this->_quote = new Mage_Sales_Model_Quote();

        $this->_quote->setData(array(
            'store_id'          => 1,
            'is_active'         => 0,
            'is_multi_shipping' => 0
        ));
        $this->_quote->save();

        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        Mage::register('isSecureArea', true);

        $this->_product->delete();
        $this->_quote->delete();

        Mage::unregister('isSecureArea');

        parent::tearDown();
    }

    /**
     * Test for product add to shopping cart
     *
     * @return void
     */
    public function testProductAddToCart()
    {
        $soapResult = $this->getWebService()->call('cart_product.add', array(
            'quoteId'  => $this->_quote->getId(),
            'products' => array(
                array('sku' => $this->_product->getSku(), 'qty' => 1)
            )
        ));

        $this->assertTrue($soapResult, 'Error during product add to cart via API call');
    }

    /**
     * Test for product with custom options add to shopping cart
     *
     * @return void
     */
    public function testProductWithCustomOptionsAddToCart()
    {
        // Create custom option for product
        $customOptionId    = null;
        $customOptionTitle = 'My Text ' . uniqid();
        $customOptionValue = 'Hello WORLD';

        $this->_product->setCanSaveCustomOptions(true);
        $this->_product->setProductOptions(array(
            array('type' => 'field', 'title' => $customOptionTitle, 'is_require' => 0, 'max_characters' => 0)
        ));
        $this->_product->save();

        // Find ID of created custom option for future use
        /** @var $productOption Mage_Catalog_Model_Product_Option */
        $productOption = Mage::getModel('catalog/product_option');

        foreach ($productOption->getProductOptionCollection($this->_product) as $option) {
            if ($option['default_title'] == $customOptionTitle) {
                $customOptionId = $option['option_id'];
                break;
            }
        }
        if (null === $customOptionId) {
            $this->fail('Can not find custom option ID that been created');
        }

        // Add product with custom option to cart via API
        if (TESTS_WEBSERVICE_TYPE == Magento_Test_Webservice::TYPE_SOAPV2) { // use associative array for SOAP v2
            $customOptionsData = array(array('key' => $customOptionId, 'value' => $customOptionValue));
        } else { // use numeric array otherwise
            $customOptionsData = array($customOptionId => $customOptionValue);
        }
        $soapResult = $this->getWebService()->call('cart_product.add', array(
            'quoteId'  => $this->_quote->getId(),
            'products' => array(
                array('sku' => $this->_product->getSku(), 'qty' => 1, 'options' => $customOptionsData)
            )
        ));

        $this->assertTrue($soapResult, 'Error during product with custom options add to cart via API call');

        /** @var $quoteItemOption Mage_Sales_Model_Resource_Quote_Item_Option_Collection */
        $quoteItemOption = Mage::getResourceModel('sales/quote_item_option_collection');
        $itemOptionValue = null;

        foreach ($quoteItemOption->getOptionsByProduct($this->_product) as $row) {
            if ('option_' . $customOptionId == $row['code']) {
                $itemOptionValue = $row['value'];
                break;
            }
        }
        if (null === $itemOptionValue) {
            $this->fail('Custom option value not found in DB after API call');
        }
        $this->assertEquals(
            $customOptionValue, $itemOptionValue, 'Custom option value in DB does not match value passed by API'
        );
    }

    /**
     * Test for product list from shopping cart API method
     *
     * @return void
     */
    public function testCartProductList()
    {
        // have to re-load product for stock item set
        $this->_product->load($this->_product->getId());

        // add product as a quote item
        $this->_quote->addProduct($this->_product);
        $this->_quote->collectTotals()->save();

        $soapResult = $this->getWebService()->call('cart_product.list', array('quoteId' => $this->_quote->getId()));

        $this->assertInternalType('array', $soapResult, 'Product List call result is not an array');
        $this->assertCount(1, $soapResult, 'Product List call result contain not exactly one product');
        $this->assertArrayHasKey('name', $soapResult[0], 'Product List call result does not contain a product name');
        $this->assertEquals($this->_product->getName(), $soapResult[0]['name'], 'Product Name does not match fixture');
    }
}
