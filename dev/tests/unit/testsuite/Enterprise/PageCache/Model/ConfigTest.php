<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorCacheExists()
    {
        $readerMock = $this->getMock('Magento_Core_Model_Config_Modules_Reader', array(), array(), '', false);
        $readerMock->expects($this->never())
            ->method('loadModulesConfiguration');
        $configCacheTypeMock = $this->getMock('Magento_Core_Model_Cache_Type_Config', array(), array(), '', false);
        $configCacheTypeMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue('<config/>'));
        new Enterprise_PageCache_Model_Config($readerMock, $configCacheTypeMock);
    }

    public function testConstructorNoCacheExists()
    {
        $config = new Magento_Core_Model_Config_Base('<config/>');
        $readerMock = $this->getMock('Magento_Core_Model_Config_Modules_Reader', array(), array(), '', false);
        $readerMock->expects($this->once())
            ->method('loadModulesConfiguration')
            ->with('placeholder.xml')
            ->will($this->returnValue($config));
        $configCacheTypeMock = $this->getMock('Magento_Core_Model_Cache_Type_Config', array(), array(), '', false);
        $configCacheTypeMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue(false));
        $configCacheTypeMock->expects($this->once())
            ->method('save')
            ->with("<config/>\n");
        new Enterprise_PageCache_Model_Config($readerMock, $configCacheTypeMock);
    }
}
