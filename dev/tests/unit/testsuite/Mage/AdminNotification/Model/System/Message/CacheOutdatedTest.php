<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_AdminNotification_Model_System_Message_CacheOutdatedTest extends PHPUnit_Framework_TestCase
{

    const MD5_LENGTH = 32;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlInterfaceMock;

    /**
     * @var Mage_AdminNotification_Model_System_Message_CacheOutdated
     */
    protected $_messageModel;

    public function setUp()
    {
        $this->_authorizationMock = $this->getMock('Mage_Core_Model_Authorization', array(), array(), '', false);
        $this->_urlInterfaceMock = $this->getMock('Mage_Core_Model_UrlInterface', array(), array(), '', false);
        $this->_cacheMock = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);
        $this->_helperFactoryMock = $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = array(
            'authorization' => $this->_authorizationMock,
            'urlBuilder' => $this->_urlInterfaceMock,
            'cache' => $this->_cacheMock,
            'helperFactory' => $this->_helperFactoryMock
        );
        $this->_messageModel = $objectManagerHelper
            ->getObject('Mage_AdminNotification_Model_System_Message_CacheOutdated', $arguments);
    }

    public function testGetIdentity()
    {
        $cacheTypesMock = $this->getMock('stdClass', array('getCacheType'));
        $cacheTypesMock->expects($this->any())->method('getCacheType')->will($this->returnValue('someVal'));
        $this->_cacheMock->expects($this->any())->method('getInvalidatedTypes')
            ->will($this->returnValue(array($cacheTypesMock)));
        $this->assertEquals(self::MD5_LENGTH, strlen($this->_messageModel->getIdentity()));
    }

    /**
     * @param $expected
     * @param $allowed
     * @param $cacheTypes
     * @dataProvider isDisplayedDataProvider
     */
    public function testIsDisplayed($expected, $allowed, $cacheTypes)
    {
        $this->_authorizationMock->expects($this->once())->method('isAllowed')->will($this->returnValue($allowed));
        $this->_cacheMock->expects($this->any())->method('getInvalidatedTypes')
            ->will($this->returnValue($cacheTypes));
        $this->assertEquals($expected, $this->_messageModel->isDisplayed());
    }

    public function isDisplayedDataProvider()
    {
        $cacheTypesMock = $this->getMock('stdClass', array('getCacheType'));
        $cacheTypesMock->expects($this->any())->method('getCacheType')->will($this->returnValue('someVal'));
        $cacheTypes = array($cacheTypesMock, $cacheTypesMock);
        return array(
            array(false, false, array()),
            array(false, false, $cacheTypes),
            array(false, true, array()),
            array(true, true, $cacheTypes)
        );
    }

    public function testGetText()
    {
        $messageText = 'One or more of the Cache Types are invalidated';

        $dataHelperMock = $this->getMock('Mage_AdminNotification_Helper_Data', array(), array(), '', false);
        $this->_helperFactoryMock->expects($this->once())->method('get')->will($this->returnValue($dataHelperMock));
        $dataHelperMock->expects($this->atLeastOnce())->method('__')->will($this->returnValue($messageText));
        $this->_cacheMock->expects($this->any())->method('getInvalidatedTypes')->will($this->returnValue(array()));
        $this->_urlInterfaceMock->expects($this->once())->method('getUrl')->will($this->returnValue('someURL'));
        $this->assertContains($messageText, $this->_messageModel->getText());
    }

    public function testGetLink()
    {
        $url = 'backend/admin/cache';
        $this->_urlInterfaceMock->expects($this->once())->method('getUrl')->will($this->returnValue($url));
        $this->assertEquals($url, $this->_messageModel->getLink());
    }
}