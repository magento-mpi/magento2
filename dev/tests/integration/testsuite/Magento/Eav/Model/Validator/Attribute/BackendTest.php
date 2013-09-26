<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Magento_Eav_Model_Validator_Attribute_Backend
 */
class Magento_Eav_Model_Validator_Attribute_BackendTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Eav_Model_Validator_Attribute_Backend
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_Eav_Model_Validator_Attribute_Backend();
    }

    /**
     * Test method for Magento_Eav_Model_Validator_Attribute_Backend::isValid
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsValid()
    {
        /** @var $entity Magento_Customer_Model_Customer */
        $entity = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Customer_Model_Customer')->load(1);

        $this->assertTrue($this->_model->isValid($entity));
        $this->assertEmpty($this->_model->getMessages());

        $entity->setData('email', null);
        $this->assertFalse($this->_model->isValid($entity));
        $this->assertArrayHasKey('email', $this->_model->getMessages());

        $entity->setData('store_id', null);
        $this->assertFalse($this->_model->isValid($entity));
        $this->assertArrayHasKey('email', $this->_model->getMessages());
        $this->assertArrayHasKey('store_id', $this->_model->getMessages());
    }
}
