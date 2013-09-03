<?php
/**
 * Unit test for model Magento_User_Model_User
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_User_Model_User testing
 */
class Magento_User_Model_UserTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_User_Model_User */
    protected $_model;

    /** @var Magento_Core_Model_Sender|PHPUnit_Framework_MockObject_MockObject */
    protected $_senderMock;

    /** @var Magento_Core_Model_Context|PHPUnit_Framework_MockObject_MockObject */
    protected $_contextMock;

    /** @var Magento_Customer_Model_Resource_Customer_Collection|PHPUnit_Framework_MockObject_MockObject */
    protected $_resourceMock;

    /** @var \Magento\Data\Collection\Db|PHPUnit_Framework_MockObject_MockObject */
    protected $_collectionMock;

    /**
     * Set required values
     */
    public function setUp()
    {
        $this->_senderMock = $this->getMockBuilder('Magento_Core_Model_Sender')
            ->disableOriginalConstructor()
            ->setMethods(array('send'))
            ->getMock();
        $this->_contextMock = $this->getMockBuilder('Magento_Core_Model_Context')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_resourceMock = $this->getMockBuilder('Magento_Customer_Model_Resource_Address')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_collectionMock = $this->getMockBuilder('Magento\Data\Collection\Db')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $this->_model = new Magento_User_Model_User(
            $this->_senderMock, $this->_contextMock, $this->_resourceMock,
            $this->_collectionMock
        );
    }

    public function testSendPasswordResetNotificationEmail()
    {
        $storeId = 0;
        $email = 'test@example.com';
        $firstName = 'Foo';
        $lastName = 'Bar';

        $this->_model->setEmail($email);
        $this->_model->setFirstname($firstName);
        $this->_model->setLastname($lastName);

        $this->_senderMock->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo($email),
                $this->equalTo($firstName . ' ' . $lastName),
                $this->equalTo(Magento_User_Model_User::XML_PATH_RESET_PASSWORD_TEMPLATE),
                $this->equalTo(Magento_User_Model_User::XML_PATH_FORGOT_EMAIL_IDENTITY),
                $this->equalTo(array('user' => $this->_model)),
                $storeId
            );
        $this->_model->sendPasswordResetNotificationEmail();
    }
}
