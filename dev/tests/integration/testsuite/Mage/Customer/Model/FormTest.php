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

/**
 * @group module:Mage_Customer
 */
class Mage_Customer_Model_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Model_Form
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= new Mage_Customer_Model_Form();
        $this->_model->setFormCode('customer_account_create');
    }

    public function testGetAttributes()
    {
        $attributes = $this->_model->getAttributes();
        $this->assertInternalType('array', $attributes);
        $this->assertNotEmpty($attributes);
    }
}
