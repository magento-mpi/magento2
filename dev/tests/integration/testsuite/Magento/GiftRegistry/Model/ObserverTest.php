<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Model\Entity
     */
    protected $_giftRegistry;

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testDeleteProduct()
    {
        Mage::register('isSecureArea', true);

        $customer = Mage::getModel('Magento\Customer\Model\Customer');
        $customer->setWebsiteId(1);
        $customer->loadByEmail('customer@example.com');

        $this->_giftRegistry = Mage::getModel('Magento\GiftRegistry\Model\Entity');
        $this->_giftRegistry->setCustomerId($customer->getId());
        $this->_giftRegistry->setTypeId(1);
        $this->_giftRegistry->setWebsiteId(1);
        $this->_giftRegistry->setIsPublic(1);
        $this->_giftRegistry->setIsActive(1);
        $this->_giftRegistry->setTitle('Test');
        $this->_giftRegistry->setMessage('Test');
        $this->_giftRegistry->save();

        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1); // fixture

        $model = Mage::getModel('Magento\Catalog\Model\Product\Type\Configurable');

        $attributes = $model->getConfigurableAttributesAsArray($product);
        $attribute = reset($attributes);
        $optionValueId = $attribute['values'][0]['value_index'];

        $buyRequest = new \Magento\Object(array(
            'qty' => 5,
            'super_attribute' => array($attribute['attribute_id'] => $optionValueId)
        ));
        $item = $this->_giftRegistry->addItem($product->getId(), $buyRequest);
        $items = $this->_giftRegistry->getItemsCollection();
        $this->assertInstanceOf('\Magento\GiftRegistry\Model\Item', $item);
        $this->assertNotEmpty($items->count());

        $cartProduct = $model->prepareForCart($buyRequest, $product);
        $simple = null;
        foreach ($cartProduct as $product) {
            if ($product->getParentId()) {
                $simple = $product;
            }
        }

        $this->assertInstanceOf('\Magento\Catalog\Model\Product', $simple);
        $simple->delete();

        $giftRegistryTwo = Mage::getModel('Magento\GiftRegistry\Model\Entity');
        $giftRegistryTwo->load($this->_giftRegistry->getId());
        $itemsTwo = $giftRegistryTwo->getItemsCollection();
        $this->assertEmpty($itemsTwo->count());
    }

}
