<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Oauth
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Oauth consumer model
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Model_ConsumerTest extends Mage_PHPUnit_TestCase
{
    /**
     * Valid url
     */
    const VALID_URL = 'http://localhost.com';

    /**
     * Invalid url
     */
    const INVALID_URL = 'localhost';

    /**
     * OAuth consumer model instance
     *
     * @var Mage_Oauth_Model_Consumer
     */
    protected $_consumer;

    /**
     * Length validator mock. Need for autoloader
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_lengthValidatorMock;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        /** @var $helper Mage_Oauth_Helper_Data */
        $helper = Mage::helper('Mage_Oauth_Helper_Data');

        $this->_consumer = Mage::getModel('Mage_Oauth_Model_Consumer');
        $this->_consumer->setData(array(
            'name'         => 'Consumer name',
            'key'          => $helper->generateConsumerKey(),
            'secret'       => $helper->generateConsumerSecret(),
            'callback_url' => self::VALID_URL,
            'rejected_callback_url' => self::VALID_URL
        ));

        $this->_lengthValidatorMock = $this->getModelMockBuilder('Mage_Oauth_Model_Consumer_Validator_KeyLength')
            ->setMethods(array('isValid'))
            ->getMock();

        $this->_lengthValidatorMock->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));
    }

    /**
     * Test consumer data validation
     */
    public function testValidation()
    {
        $this->assertTrue($this->_consumer->validate());
    }

    /**
     * Test invalid callback URL validation
     */
    public function testCallbackUrlValidationInvalid()
    {

        $this->_consumer->setCallbackUrl(self::INVALID_URL);
        $this->setExpectedException('Mage_Core_Exception', 'Invalid Callback URL');

        $this->_consumer->validate();
    }

    /**
     * Test empty callback URL validation
     */
    public function testCallbackUrlValidationEmpty()
    {
        $this->_consumer->setCallbackUrl('');
        $this->assertTrue($this->_consumer->validate());
    }

    /**
     * Test invalid rejected callback URL validation
     */
    public function testRejectedCallbackUrlValidationInvalid()
    {
        $this->_consumer->setRejectedCallbackUrl(self::INVALID_URL);
        $this->setExpectedException('Mage_Core_Exception', 'Invalid Rejected Callback URL');

        $this->_consumer->validate();
    }

    /**
     * Test empty rejected callback URL validation
     */
    public function testRejectedCallbackUrlValidationEmpty()
    {
        $this->_consumer->setRejectedCallbackUrl('');
        $this->assertTrue($this->_consumer->validate());
    }
}
