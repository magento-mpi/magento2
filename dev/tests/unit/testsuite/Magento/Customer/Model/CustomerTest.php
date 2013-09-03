<?php
/**
 * Unit test for customer service layer Magento_Customer_Model_Customer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Customer_Model_Customer testing
 */
class Magento_Customer_Model_CustomerTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Customer_Model_Customer */
    protected $_model;

    /** @var Magento_Core_Model_Website|PHPUnit_Framework_MockObject_MockObject */
    protected $_website;

    /** @var Magento_Core_Model_Sender|PHPUnit_Framework_MockObject_MockObject */
    protected $_senderMock;

    /** @var Magento_Core_Model_StoreManager|PHPUnit_Framework_MockObject_MockObject */
    protected $_storeManager;

    /** @var Magento_Eav_Model_Config|PHPUnit_Framework_MockObject_MockObject */
    protected $_config;

    /** @var Magento_Eav_Model_Attribute|PHPUnit_Framework_MockObject_MockObject */
    protected $_attribute;

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
        $this->_website = $this->getMockBuilder('Magento_Core_Model_Website')
            ->disableOriginalConstructor()
            ->setMethods(array('getStoreIds'))
            ->getMock();
        $this->_senderMock = $this->getMockBuilder('Magento_Core_Model_Sender')
            ->disableOriginalConstructor()
            ->setMethods(array('send'))
            ->getMock();
        $this->_storeManager = $this->getMockBuilder('Magento_Core_Model_StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getWebsite'))
            ->getMock();
        $this->_config = $this->getMockBuilder('Magento_Eav_Model_Config')
            ->disableOriginalConstructor()
            ->setMethods(array('getAttribute'))
            ->getMock();
        $this->_attribute = $this->getMockBuilder('Magento_Eav_Model_Attribute')
            ->disableOriginalConstructor()
            ->setMethods(array('getIsVisible'))
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

        $this->_model = new Magento_Customer_Model_Customer(
            $this->_contextMock, $this->_senderMock, $this->_storeManager, $this->_config, $this->_resourceMock,
            $this->_collectionMock, array()
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

        $this->_attribute->expects($this->any())->method('getIsVisible')->will($this->returnValue(false));

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
                $this->equalTo(Magento_Customer_Model_Customer::XML_PATH_RESET_PASSWORD_TEMPLATE),
                $this->equalTo(Magento_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_IDENTITY),
                $this->equalTo(array('customer' => $this->_model)),
                $storeId
        );
        $this->_model->sendPasswordResetNotificationEmail();
    }
}
