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

    /** @var Magento_User_Helper_Data */
    protected $_userData;

    /** @var Magento_Core_Helper_Data */
    protected $_coreData;

    /** @var Magento_Core_Model_Sender|PHPUnit_Framework_MockObject_MockObject */
    protected $_senderMock;

    /** @var Magento_Core_Model_Context|PHPUnit_Framework_MockObject_MockObject */
    protected $_contextMock;

    /** @var Magento_User_Model_Resource_User|PHPUnit_Framework_MockObject_MockObject */
    protected $_resourceMock;

    /** @var Magento_Data_Collection_Db|PHPUnit_Framework_MockObject_MockObject */
    protected $_collectionMock;

    /**
     * Set required values
     */
    protected function setUp()
    {
        $this->_userData = $this->getMockBuilder('Magento_User_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_coreData = $this->getMockBuilder('Magento_Core_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_senderMock = $this->getMockBuilder('Magento_Core_Model_Sender')
            ->disableOriginalConstructor()
            ->setMethods(array('send'))
            ->getMock();
        $this->_contextMock = $this->getMockBuilder('Magento_Core_Model_Context')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_resourceMock = $this->getMockBuilder('Magento_User_Model_Resource_User')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_collectionMock = $this->getMockBuilder('Magento_Data_Collection_Db')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $coreRegistry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);

        $eventManagerMock = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $objectFactoryMock = $this->getMock('Magento_Validator_Composite_VarienObjectFactory', array('create'),
            array(), '', false);
        $roleFactoryMock = $this->getMock('Magento_User_Model_RoleFactory', array('create'),
            array(), '', false);
        $emailFactoryMock = $this->getMock('Magento_Core_Model_Email_InfoFactory', array('create'),
            array(), '', false);
        $mailerFactoryMock = $this->getMock('Magento_Core_Model_Email_Template_MailerFactory', array('create'),
            array(), '', false);

        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $helper->getObject('Magento_User_Model_User', array(
            'eventManager' => $eventManagerMock,
            'userData' => $this->_userData,
            'coreData' => $this->_coreData,
            'sender' => $this->_senderMock,
            'context' => $this->_contextMock,
            'registry' => $coreRegistry,
            'resource' => $this->_resourceMock,
            'resourceCollection' => $this->_collectionMock,
            'validatorCompositeFactory' => $objectFactoryMock,
            'roleFactory' => $roleFactoryMock,
            'emailInfoFactory' => $emailFactoryMock,
            'mailerFactory' => $mailerFactoryMock,
        ));
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
