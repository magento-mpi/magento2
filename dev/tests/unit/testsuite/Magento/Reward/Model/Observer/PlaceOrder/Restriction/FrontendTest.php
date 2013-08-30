<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Reward_Model_Observer_PlaceOrder_Restriction_FrontendTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Reward_Model_Observer_PlaceOrder_Restriction_Frontend
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = $this->getMock('Magento_Reward_Helper_Data', array(), array(), '', false);
        $this->_model = new Magento_Reward_Model_Observer_PlaceOrder_Restriction_Frontend(
            $this->_helper
        );
    }

    public function testIsAllowed()
    {
        $this->_helper->expects($this->once())->method('isEnabledOnFront');
        $this->_model->isAllowed();
    }
}
