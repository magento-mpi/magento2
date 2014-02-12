<?php
/**
 * Unit test for customer service layer \Magento\Customer\Model\Customer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Customer\Model\Customer testing
 */
namespace Magento\Customer\Model;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Model\Customer */
    protected $_model;

    /** @var \Magento\Core\Model\Website|\PHPUnit_Framework_MockObject_MockObject */
    protected $_website;

    /** @var \Magento\Core\Model\Sender|\PHPUnit_Framework_MockObject_MockObject */
    protected $_senderMock;

    /** @var \Magento\Core\Model\StoreManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $_storeManager;

    /** @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $_config;

    /** @var \Magento\Eav\Model\Attribute|\PHPUnit_Framework_MockObject_MockObject */
    protected $_attribute;

    /**
     * Set required values
     */
    protected function setUp()
    {
        $this->_website = $this->getMock('Magento\Core\Model\Website', array(), array(), '', false);
        $this->_senderMock = $this->getMock('Magento\Email\Model\Sender', array(), array(), '', false);
        $this->_config = $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false);
        $this->_attribute = $this->getMock('Magento\Eav\Model\Attribute', array(), array(), '', false);
        $this->_storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject('Magento\Customer\Model\Customer', array(
            'sender' => $this->_senderMock,
            'storeManager' => $this->_storeManager,
            'config' =>  $this->_config
            )
        );
    }

    public function testSendPasswordResetConfirmationEmail()
    {
        $storeId = 1;
        $storeIds = array(1);
        $email = 'test@example.com';
        $firstName = 'Foo';
        $lastName = 'Bar';

        $this->_model->setStoreId(0);
        $this->_model->setWebsiteId(1);
        $this->_model->setEmail($email);
        $this->_model->setFirstname($firstName);
        $this->_model->setLastname($lastName);

        $this->_config->expects($this->any())->method('getAttribute')->will($this->returnValue($this->_attribute));

        $this->_attribute->expects($this->any())->method('isVisible')->will($this->returnValue(false));

        $this->_storeManager->expects($this->once())
            ->method('getWebsite')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->_website));

        $this->_website->expects($this->once())->method('getStoreIds')->will($this->returnValue($storeIds));

        $this->_senderMock->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo($email),
                $this->equalTo($firstName . ' ' . $lastName),
                $this->equalTo(\Magento\Customer\Model\Customer::XML_PATH_RESET_PASSWORD_TEMPLATE),
                $this->equalTo(\Magento\Customer\Model\Customer::XML_PATH_FORGOT_EMAIL_IDENTITY),
                $this->equalTo(array('customer' => $this->_model)),
                $storeId
        );
        $this->_model->sendPasswordResetNotificationEmail();
    }
}
