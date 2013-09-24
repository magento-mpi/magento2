<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_CookieTest extends PHPUnit_Framework_TestCase
{
    /**
     * Name of the sample cookie to be used in tests
     */
    const SAMPLE_COOKIE_NAME = 'sample_cookie';

    /**
     * @var Magento_TestFramework_Cookie
     */
    protected $_model;

    protected function setUp()
    {
        $helperMock = $this->getMock('Magento_Backend_Helper_Data', array(), array(),
            'Magento_Backend_Helper_DataProxy', false);
        $coreStoreConfig = $this->getMockBuilder('Magento_Core_Model_Store_Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = new Magento_TestFramework_Cookie(
            $coreStoreConfig,
            new Magento_TestFramework_Request($helperMock),
            new Magento_TestFramework_Response(
                $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false)
            )
        );
    }

    public function testSet()
    {
        $cookieValue = 'some_cookie_value';
        $this->assertFalse($this->_model->get(self::SAMPLE_COOKIE_NAME));
        $this->_model->set(self::SAMPLE_COOKIE_NAME, $cookieValue);
        $this->assertEquals($cookieValue, $this->_model->get(self::SAMPLE_COOKIE_NAME));
        $this->assertEquals($cookieValue, $_COOKIE[self::SAMPLE_COOKIE_NAME]);
    }

    public function testDelete()
    {
        $this->_model->set(self::SAMPLE_COOKIE_NAME, 'some_value');
        $this->_model->delete(self::SAMPLE_COOKIE_NAME);
        $this->assertFalse($this->_model->get(self::SAMPLE_COOKIE_NAME));
        $this->assertArrayNotHasKey(self::SAMPLE_COOKIE_NAME, $_COOKIE);
    }
}
