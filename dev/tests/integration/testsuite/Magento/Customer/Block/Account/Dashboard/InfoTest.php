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

class Magento_Customer_Block_Account_Dashboard_InfoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Block\Account\Dashboard\Info
     */
    protected $_block;

    public function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Magento\Customer\Block\Account\Dashboard\Info');
    }

    public function testGetSubscriptionObject()
    {
        $object = $this->_block->getSubscriptionObject();
        $this->assertInstanceOf('\Magento\Newsletter\Model\Subscriber', $object);

        $object2 = $this->_block->getSubscriptionObject();
        $this->assertSame($object, $object2);
    }
}
