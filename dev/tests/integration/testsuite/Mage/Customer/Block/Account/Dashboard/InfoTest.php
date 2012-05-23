<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Customer_Block_Account_Dashboard_InfoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Block_Account_Dashboard_Info
     */
    protected $_block;

    public function setUp()
    {
        $this->_block = new Mage_Customer_Block_Account_Dashboard_Info;
    }

    public function tearDown()
    {
        $this->_block = null;
    }

    public function testGetSubscriptionObject()
    {
        $object = $this->_block->getSubscriptionObject();
        $this->assertInstanceOf('Mage_Newsletter_Model_Subscriber', $object);

        $object2 = $this->_block->getSubscriptionObject();
        $this->assertSame($object, $object2);
    }
}
