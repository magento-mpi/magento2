<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model;

class VisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Log\Model\Visitor
     */
    protected $_model;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject('Magento\Log\Model\Visitor');
    }

    public function testBindCustomerLogin()
    {
        $customer = new \Magento\Object(['id' => '1']);
        $observer = new \Magento\Object([
            'event' => new \Magento\Object(['customer' => $customer])
        ]);

        $this->_model->bindCustomerLogin($observer);
        $this->assertTrue($this->_model->getDoCustomerLogin());
        $this->assertEquals($customer->getId(), $this->_model->getCustomerId());

        $this->_model->unsetData();
        $this->_model->setCustomerId('2');
        $this->_model->bindCustomerLogin($observer);
        $this->assertNull($this->_model->getDoCustomerLogin());
        $this->assertEquals('2', $this->_model->getCustomerId());
    }

    public function testBindCustomerLogout()
    {
        $observer = new \Magento\Object();

        $this->_model->setCustomerId('1');
        $this->_model->bindCustomerLogout($observer);
        $this->assertTrue($this->_model->getDoCustomerLogout());

        $this->_model->unsetData();
        $this->_model->bindCustomerLogout($observer);
        $this->assertNull($this->_model->getDoCustomerLogout());
    }
}
