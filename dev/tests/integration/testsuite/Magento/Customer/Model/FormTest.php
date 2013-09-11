<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Model_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Form
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= Mage::getModel('Magento\Customer\Model\Form');
        $this->_model->setFormCode('customer_account_create');
    }

    public function testGetAttributes()
    {
        $attributes = $this->_model->getAttributes();
        $this->assertInternalType('array', $attributes);
        $this->assertNotEmpty($attributes);
    }
}
