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
 * Test for \Magento\Eav\Model\Validator\Attribute\Backend
 */
namespace Magento\Eav\Model\Validator\Attribute;

class BackendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Validator\Attribute\Backend
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Eav\Model\Validator\Attribute\Backend();
    }

    /**
     * Test method for \Magento\Eav\Model\Validator\Attribute\Backend::isValid
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsValid()
    {
        /** @var $entity \Magento\Customer\Model\Customer */
        $entity = \Mage::getModel('Magento\Customer\Model\Customer')->load(1);

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
