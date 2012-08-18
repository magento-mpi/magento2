<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_GiftRegistry_Model_Entity
     */
    protected $_giftRegistry;

    protected function tearDown()
    {
        $this->_giftRegistry = null;
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_configurable.php
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testDeleteProduct()
    {
        Mage::register('isSecureArea', true);

        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->setWebsiteId(1);
        $customer->loadByEmail('customer@example.com');

        $this->_giftRegistry = Mage::getModel('Enterprise_GiftRegistry_Model_Entity');
        $this->_giftRegistry->setCustomerId($customer->getId());
        $this->_giftRegistry->setTypeId(1);
        $this->_giftRegistry->setWebsiteId(1);
        $this->_giftRegistry->setIsPublic(1);
        $this->_giftRegistry->setIsActive(1);
        $this->_giftRegistry->setTitle('Test');
        $this->_giftRegistry->setMessage('Test');
        $this->_giftRegistry->save();

        $product = new Mage_Catalog_Model_Product;
        $product->load(1); // fixture

        $model = new Mage_Catalog_Model_Product_Type_Configurable;

        $attributes = $model->getConfigurableAttributesAsArray($product);
        $confAttribute = $attributes[0];
        $optionValueId = $confAttribute['values'][0]['value_index'];

        $buyRequest = new Varien_Object(array(
            'qty' => 5, 'super_attribute' => array($confAttribute['attribute_id'] => $optionValueId)
        ));
        $item = $this->_giftRegistry->addItem($product->getId(), $buyRequest);
        $items = $this->_giftRegistry->getItemsCollection();
        $this->assertInstanceOf('Enterprise_GiftRegistry_Model_Item', $item);
        $this->assertNotEmpty($items->count());

        $cartProduct = $model->prepareForCart($buyRequest, $product);
        $simple = null;
        foreach ($cartProduct as $product) {
            if ($product->getParentId()) {
                $simple = $product;
            }
        }

        $this->assertInstanceOf('Mage_Catalog_Model_Product', $simple);
        $simple->delete();

        $giftRegistry2 = Mage::getModel('Enterprise_GiftRegistry_Model_Entity');
        $giftRegistry2->load($this->_giftRegistry->getId());
        $items2 = $giftRegistry2->getItemsCollection();
        $this->assertEmpty($items2->count());
    }

}
