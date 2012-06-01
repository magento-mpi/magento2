<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_Fallback_Caching_ProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that model delegates non-resolution calls to a fallback model
     */
    public function testTransparentBehaviour()
    {
        $expected = 'expected value';

        $fallback = $this->getMock('Mage_Core_Model_Design_Fallback', array('someMethod'));
        $fallback->expects($this->once())
            ->method('someMethod')
            ->with('param1', 'param2')
            ->will($this->returnValue($expected));

        /** @var $model Mage_Core_Model_Design_Fallback_Caching_Proxy */
        $model = $this->getMock('Mage_Core_Model_Design_Fallback_Caching_Proxy', array('_getFallback'),
            array(), '', false);
        $model->expects($this->once())
            ->method('_getFallback')
            ->will($this->returnValue($fallback));

        $actual = $model->someMethod('param1', 'param2');
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that proxy, if entry is not found in a map,
     * a) successfully delegates its resolution to a Fallback model
     * b) puts into a map, and subsequent calls do not use Fallback model
     *
     * Every call is repeated twice to verify, that fallback is used only once and next time a proper value is returned
     * by map.
     */
    public function testOperations()
    {
        $fallback = $this->getMock('Mage_Core_Model_Design_Fallback', array('getFile', 'getLocaleFile', 'getSkinFile'));
        $map = $this->getMock('Mage_Core_Model_Design_Fallback_Map', array('save'), array(__DIR__));

        /** @var $model Mage_Core_Model_Design_Fallback_Caching_Proxy */
        $model = $this->getMock(
            'Mage_Core_Model_Design_Fallback_Caching_Proxy',
            array('_getFallback', '_getMap'),
            array(),
            '',
            false
        );
        $model->expects($this->any())
            ->method('_getFallback')
            ->will($this->returnValue($fallback));
        $model->expects($this->any())
            ->method('_getMap')
            ->will($this->returnValue($map));


        // getFile()
        $expected = 'path/to/theme_file.ext';
        $fallback->expects($this->once())
            ->method('getFile')
            ->with('file.ext', 'area', 'package', 'theme', 'module')
            ->will($this->returnValue($expected));

        $actual = $model->getFile('file.ext', 'area', 'package', 'theme', 'module');
        $this->assertEquals($expected, $actual);
        $actual = $model->getFile('file.ext', 'area', 'package', 'theme', 'module');
        $this->assertEquals($expected, $actual);

        // getLocaleFile()
        $expected = 'path/to/locale_file.ext';
        $fallback->expects($this->once())
            ->method('getLocaleFile')
            ->with('file.ext', 'area', 'package', 'theme', 'locale')
            ->will($this->returnValue($expected));

        $actual = $model->getLocaleFile('file.ext', 'area', 'package', 'theme', 'locale');
        $this->assertEquals($expected, $actual);
        $actual = $model->getLocaleFile('file.ext', 'area', 'package', 'theme', 'locale');
        $this->assertEquals($expected, $actual);

        // getSkinFile()
        $expected = 'path/to/skin_file.ext';
        $fallback->expects($this->once())
            ->method('getSkinFile')
            ->with('file.ext', 'area', 'package', 'theme', 'skin', 'locale', 'module')
            ->will($this->returnValue($expected));

        $actual = $model->getSkinFile('file.ext', 'area', 'package', 'theme', 'skin', 'locale', 'module');
        $this->assertEquals($expected, $actual);
        $actual = $model->getSkinFile('file.ext', 'area', 'package', 'theme', 'skin', 'locale', 'module');
        $this->assertEquals($expected, $actual);
    }

    public function testSetFilePath()
    {
        $expectedFilePath = 'path/to/skin_file.ext';
        $map = $this->getMock('Mage_Core_Model_Design_Fallback_Map', array('setFilePath'), array(__DIR__));
        $map->expects($this->once())
            ->method('setFilePath')
            ->with('file.ext', 'area', 'package', 'theme', 'skin', 'locale', 'module', $expectedFilePath);


        /** @var $model Mage_Core_Model_Design_Fallback_Caching_Proxy */
        $model = $this->getMock(
            'Mage_Core_Model_Design_Fallback_Caching_Proxy',
            array('_getMap'),
            array(),
            '',
            false
        );
        $model->expects($this->atLeastOnce())
            ->method('_getMap')
            ->will($this->returnValue($map));

        $model->setFilePath('file.ext', 'area', 'package', 'theme', 'skin', 'locale', 'module',
            $expectedFilePath);
    }

    public function testSaveMap()
    {
        $map = $this->getMock('Mage_Core_Model_Design_Fallback_Map', array(), array(__DIR__));
        $map->expects($this->once())
            ->method('save');

        /** @var $model Mage_Core_Model_Design_Fallback_Caching_Proxy */
        $model = $this->getMock(
            'Mage_Core_Model_Design_Fallback_Caching_Proxy',
            array('_getMap'),
            array(),
            '',
            false
        );
        $model->expects($this->once())
            ->method('_getMap')
            ->will($this->returnValue($map));

        $model->saveMap();
    }
}
