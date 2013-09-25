<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Captcha_Model_DefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * Captcha default config data
     * @var array
     */
    protected static $_defaultConfig = array(
        'type' => 'default',
        'enable' => '1',
        'font' => 'linlibertine',
        'mode' => 'after_fail',
        'forms' => 'user_forgotpassword,user_create,guest_checkout,register_during_checkout',
        'failed_attempts_login' => '3',
        'failed_attempts_ip' => '1000',
        'timeout' => '7',
        'length' => '4-5',
        'symbols' => 'ABCDEFGHJKMnpqrstuvwxyz23456789',
        'case_sensitive' => '0',
        'shown_to_logged_in_user' => array(
            'contact_us' => 1,
        ),
        'always_for' => array(
            'user_create',
            'user_forgotpassword',
            'guest_checkout',
            'register_during_checkout',
            'contact_us',
        ),
    );

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * path to fonts
     * @var array
     */
    protected $_fontPath = array(
        'LinLibertine' => array(
            'label' => 'LinLibertine',
            'path' => 'lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf'
        )
    );

    /**
     * @var Magento_Captcha_Model_Default
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resLogFactory;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->session = $this->_getSessionStub();

        $this->_storeManager = $this->getMock('Magento_Core_Model_StoreManager', array('getStore'), array(), '', false);
        $this->_storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_getStoreStub()));

        // Magento_Customer_Model_Session
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_objectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                'Magento_Captcha_Helper_Data' => $this->_getHelperStub(),
                'Magento_Customer_Model_Session' => $this->session,
            )));


        $this->_resLogFactory = $this->getMock('Magento_Captcha_Model_Resource_LogFactory',
            array('create'), array(), '', false);
        $this->_resLogFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_getResourceModelStub()));

        $this->_object = new Magento_Captcha_Model_Default(
            $this->session,
            $this->_getHelperStub(),
            $this->_resLogFactory,
            'user_create'
        );
    }

    /**
     * @covers Magento_Captcha_Model_Default::getBlockName
     */
    public function testGetBlockName()
    {
        $this->assertEquals($this->_object->getBlockName(), 'Magento_Captcha_Block_Captcha_Default');
    }

    /**
     * @covers Magento_Captcha_Model_Default::isRequired
     */
    public function testIsRequired()
    {
        $this->assertTrue($this->_object->isRequired());
    }

    /**
     * @covers Magento_Captcha_Model_Default::isCaseSensitive
     */
    public function testIsCaseSensitive()
    {
        self::$_defaultConfig['case_sensitive'] = '1';
        $this->assertEquals($this->_object->isCaseSensitive(), '1');
        self::$_defaultConfig['case_sensitive'] = '0';
        $this->assertEquals($this->_object->isCaseSensitive(), '0');
    }

    /**
     * @covers Magento_Captcha_Model_Default::getFont
     */
    public function testGetFont()
    {
        $this->assertEquals(
            $this->_object->getFont(),
            $this->_fontPath['LinLibertine']['path']
        );
    }

    /**
     * @covers Magento_Captcha_Model_Default::getTimeout
     * @covers Magento_Captcha_Model_Default::getExpiration
     */
    public function testGetTimeout()
    {
        $this->assertEquals(
            $this->_object->getTimeout(),
            self::$_defaultConfig['timeout'] * 60
        );
    }

    /**
     * @covers Magento_Captcha_Model_Default::isCorrect
     */
    public function testIsCorrect()
    {
        self::$_defaultConfig['case_sensitive'] = '1';
        $this->assertFalse($this->_object->isCorrect('abcdef5'));
        $sessionData = array(
            'user_create_word' => array(
                'data' => 'AbCdEf5',
                'expires' => time() + 600
            )
        );
        $this->_object->getSession()->setData($sessionData);
        self::$_defaultConfig['case_sensitive'] = '0';
        $this->assertTrue($this->_object->isCorrect('abcdef5'));
    }

    /**
     * @covers Magento_Captcha_Model_Default::getImgSrc
     */
    public function testGetImgSrc()
    {
        $this->assertEquals(
            $this->_object->getImgSrc(),
            'http://localhost/pub/media/captcha/base/' . $this->_object->getId() . '.png'
        );
    }

    /**
     * @covers Magento_Captcha_Model_Default::logAttempt
     */
    public function testLogAttempt()
    {
        $captcha = new Magento_Captcha_Model_Default(
            $this->session,
            $this->_getHelperStub(),
            $this->_resLogFactory,
            'user_create'
        );

        $captcha->logAttempt('admin');

        $this->assertEquals($captcha->getSession()->getData('user_create_show_captcha'), 1);
    }

    /**
     * @covers Magento_Captcha_Model_Default::getWord
     */
    public function testGetWord()
    {
        $this->assertEquals($this->_object->getWord(), 'AbCdEf5');
        $this->_object->getSession()->setData(
            array(
                'user_create_word' => array(
                    'data' => 'AbCdEf5',
                    'expires' => time() - 60
                )
            )
        );
        $this->assertNull($this->_object->getWord());
    }

    /**
     * Create stub session object
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getSessionStub()
    {
        $session = $this->getMock('Magento_Customer_Model_Session', array('isLoggedIn'), array(), '', false);
        $session->expects($this->any())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $session->setData(
            array(
                'user_create_word' => array(
                    'data' => 'AbCdEf5',
                    'expires' => time() + 600
                )
            )
        );
        return $session;
    }

    /**
     * Create helper stub
     * @return Magento_Captcha_Helper_Data
     */
    protected function _getHelperStub()
    {
        $helper = $this->getMockBuilder('Magento_Captcha_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getConfigNode', 'getFonts', '_getWebsiteCode', 'getImgUrl'))
            ->getMock();

        $helper->expects($this->any())
            ->method('getConfigNode')
            ->will($this->returnCallback('Magento_Captcha_Model_DefaultTest::getConfigNodeStub'));

        $helper->expects($this->any())
            ->method('getFonts')
            ->will($this->returnValue($this->_fontPath));

        $helper->expects($this->any())
            ->method('_getWebsiteCode')
            ->will($this->returnValue('base'));

        $helper->expects($this->any())
            ->method('getImgUrl')
            ->will($this->returnValue('http://localhost/pub/media/captcha/base/'));


        return $helper;
    }

    /**
     * Get stub for resource model
     * @return Magento_Captcha_Model_Resource_Log
     */
    protected function _getResourceModelStub()
    {
        $resourceModel = $this->getMock(
            'Magento_Captcha_Model_Resource_Log',
            array('countAttemptsByRemoteAddress', 'countAttemptsByUserLogin', 'logAttempt'),
            array(), '', false
        );

        $resourceModel->expects($this->any())
            ->method('logAttempt');

        $resourceModel->expects($this->any())
            ->method('countAttemptsByRemoteAddress')
            ->will($this->returnValue(0));

        $resourceModel->expects($this->any())
            ->method('countAttemptsByUserLogin')
            ->will($this->returnValue(3));
        return $resourceModel;
    }

    /**
     * Mock get config method
     * @static
     * @return string
     * @throws InvalidArgumentException
     */
    public static function getConfigNodeStub()
    {
        $args = func_get_args();
        $hashName = $args[0];

        if (array_key_exists($hashName, self::$_defaultConfig)) {
            return self::$_defaultConfig[$hashName];
        }

        throw new InvalidArgumentException('Unknow id = ' . $hashName);
    }

    /**
     * Create store stub
     *
     * @return Magento_Core_Model_Store
     */
    protected function _getStoreStub()
    {
        $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));
        $store->expects($this->any())
            ->method('isAdmin')
            ->will($this->returnValue(false));
        return $store;
    }

    /**
     * @param boolean $expectedResult
     * @param string $formId
     * @dataProvider isShownToLoggedInUserDataProvider
     */
    public function testIsShownToLoggedInUser($expectedResult, $formId)
    {
        $captcha = new Magento_Captcha_Model_Default(
            $this->session,
            $this->_getHelperStub(),
            $this->_resLogFactory,
            $formId
        );
        $this->assertEquals($expectedResult, $captcha->isShownToLoggedInUser());
    }

    public function isShownToLoggedInUserDataProvider()
    {
        return array(
            array(true, 'contact_us'),
            array(false, 'user_create'),
            array(false, 'user_forgotpassword'),
            array(false, 'guest_checkout'),
        );
    }
}
