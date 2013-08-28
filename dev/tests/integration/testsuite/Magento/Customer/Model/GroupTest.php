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

class Magento_Customer_Model_GroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Customer_Model_Group
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= Mage::getModel('Magento_Customer_Model_Group');
    }

    public function testCRUD()
    {
        $this->_model->setCustomerGroupCode('test');
        $crud = new Magento_Test_Entity($this->_model, array('customer_group_code' => uniqid()));
        $crud->testCrud();
    }
}
