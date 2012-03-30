<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Oauth
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $helper = Mage::helper('oauth');

        $this->_consumer = Mage::getModel('oauth/consumer');
        $this->_consumer->setData(array(
            'name'         => 'Consumer name',
            'key'          => $helper->generateConsumerKey(),
            'secret'       => $helper->generateConsumerSecret(),
            'callback_url' => self::VALID_URL,
            'rejected_callback_url' => self::VALID_URL
        ));

        $this->_lengthValidatorMock = $this->getModelMockBuilder('oauth/consumer_validator_keyLength')
            ->setMethods(array('isValid'))
            ->getMock();
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
