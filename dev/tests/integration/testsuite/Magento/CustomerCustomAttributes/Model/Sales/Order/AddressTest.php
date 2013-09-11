<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/CustomerCustomAttributes/_files/order_address_with_attribute.php
 */
class Magento_CustomerCustomAttributes_Model_Sales_Order_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Model\Sales\Order\Address
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('\Magento\CustomerCustomAttributes\Model\Sales\Order\Address');
    }

    public function testAttachDataToEntities()
    {
        $address = Mage::getModel('\Magento\Sales\Model\Order\Address');
        $address->load('admin@example.com', 'email');

        $entity = new \Magento\Object(array('id' => $address->getId()));
        $this->assertEmpty($entity->getData('fixture_address_attribute'));
        $this->_model->attachDataToEntities(array($entity));
        $this->assertEquals('fixture_attribute_custom_value', $entity->getData('fixture_address_attribute'));
    }
}
