<?php
/**
 * Unit test for model \Magento\User\Model\User
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\User\Model\User testing
 */
class Magento_User_Model_UserTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\User\Model\User */
    protected $_model;

    /** @var \Magento\User\Helper\Data */
    protected $_userData;

    /** @var \Magento\Core\Helper\Data */
    protected $_coreData;

    /** @var \Magento\Core\Model\Sender|PHPUnit_Framework_MockObject_MockObject */
    protected $_senderMock;

    /** @var \Magento\Core\Model\Context|PHPUnit_Framework_MockObject_MockObject */
    protected $_contextMock;

    /** @var \Magento\User\Model\Resource\User|PHPUnit_Framework_MockObject_MockObject */
    protected $_resourceMock;

    /** @var \Magento\Data\Collection\Db|PHPUnit_Framework_MockObject_MockObject */
    protected $_collectionMock;

    /**
     * Set required values
     */
    protected function setUp()
    {
        $this->_userData = $this->getMockBuilder('Magento\User\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_coreData = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_senderMock = $this->getMockBuilder('Magento\Core\Model\Sender')
            ->disableOriginalConstructor()
            ->setMethods(array('send'))
            ->getMock();
        $this->_contextMock = $this->getMockBuilder('Magento\Core\Model\Context')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_resourceMock = $this->getMockBuilder('Magento\User\Model\Resource\User')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_collectionMock = $this->getMockBuilder('Magento\Data\Collection\Db')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $coreRegistry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        
        $this->_model = new Magento_User_Model_User(
            $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false),
            $this->_userData,
            $this->_coreData,
            $this->_senderMock,
            $this->_contextMock,
            $coreRegistry,
            $coreStoreConfig,
            $this->_resourceMock,
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
                $this->equalTo(\Magento\User\Model\User::XML_PATH_RESET_PASSWORD_TEMPLATE),
                $this->equalTo(\Magento\User\Model\User::XML_PATH_FORGOT_EMAIL_IDENTITY),
                $this->equalTo(array('user' => $this->_model)),
                $storeId
            );
        $this->_model->sendPasswordResetNotificationEmail();
    }
}
