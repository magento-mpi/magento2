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
     * @var Magento_CustomerCustomAttributes_Model_Sales_Order_Address
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Order_Address');
    }

    public function testAttachDataToEntities()
    {
        $address = Mage::getModel('Magento_Sales_Model_Order_Address');
        $address->load('admin@example.com', 'email');

        $entity = new Magento_Object(array('id' => $address->getId()));
        $this->assertEmpty($entity->getData('fixture_address_attribute'));
        $this->_model->attachDataToEntities(array($entity));
        $this->assertEquals('fixture_attribute_custom_value', $entity->getData('fixture_address_attribute'));
    }
}
