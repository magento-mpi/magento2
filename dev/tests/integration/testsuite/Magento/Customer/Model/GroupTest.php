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

namespace Magento\Customer\Model;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Group');
    }

    public function testCRUD()
    {
        $this->_model->setCustomerGroupCode('test');
        $crud = new \Magento\TestFramework\Entity($this->_model, array('customer_group_code' => uniqid()));
        $crud->testCrud();
    }
}
