<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Locale_ManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Locale\Manager
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    protected function setUp()
    {
        $this->_session = $this->getMock('Magento\Backend\Model\Session', array(), array(), '', false);

        $this->_authSession = $this->getMock('Magento\Backend\Model\Auth\Session',
            array('getUser'), array(), '', false);

        $userMock = new \Magento\Object();

        $this->_authSession->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($userMock));

        $this->_translator = $this->getMock('Magento\Core\Model\Translate',
            array(), array(), '', false);

        $this->_translator->expects($this->any())
            ->method('setLocale')
            ->will($this->returnValue($this->_translator));

        $this->_translator->expects($this->any())
            ->method('init')
            ->will($this->returnValue(false));

        $this->_model = new \Magento\Backend\Model\Locale\Manager($this->_session, $this->_authSession,
            $this->_translator);
    }

    /**
     * @return array
     */
    public function switchBackendInterfaceLocaleDataProvider()
    {
        return array(
            'case1' => array(
                'locale' => 'de_DE',
            ),
            'case2' => array(
                'locale' => 'en_US',
            ),
        );
    }

    /**
     * @param string $locale
     * @dataProvider switchBackendInterfaceLocaleDataProvider
     * @covers \Magento\Backend\Model\Locale\Manager::switchBackendInterfaceLocale
     */
    public function testSwitchBackendInterfaceLocale($locale)
    {
        $this->_model->switchBackendInterfaceLocale($locale);

        $userInterfaceLocale = $this->_authSession->getUser()->getInterfaceLocale();
        $this->assertEquals($userInterfaceLocale, $locale);

        $sessionLocale = $this->_session->getSessionLocale();
        $this->assertEquals($sessionLocale, null);
    }

    /**
     * @covers \Magento\Backend\Model\Locale\Manager::getUserInterfaceLocale
     */
    public function testGetUserInterfaceLocaleDefault()
    {
        $locale = $this->_model->getUserInterfaceLocale();

        $this->assertEquals($locale, \Magento\Core\Model\LocaleInterface::DEFAULT_LOCALE);
    }

    /**
     * @covers \Magento\Backend\Model\Locale\Manager::getUserInterfaceLocale
     */
    public function testGetUserInterfaceLocale()
    {
        $this->_model->switchBackendInterfaceLocale('de_DE');
        $locale = $this->_model->getUserInterfaceLocale();

        $this->assertEquals($locale, 'de_DE');
    }
}
