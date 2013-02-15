<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Varien_Cache_Core test case
 */
class Varien_Cache_CoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Cache_Core
     */
    protected $_core;

    /**
     * @var array
     */
    protected static $_cacheStorage = array();

    protected function setUp()
    {
        $this->_core = new Varien_Cache_Core();
    }

    protected function tearDown()
    {
        unset ($this->_core);
        self::$_cacheStorage = array();
    }

    /**
     * @dataProvider isCompressionAllowedProvider
     */
    public function testIsCompressionAllowed($value, $expectedResult)
    {
        $this->_core->setOption('compression', $value);

        $method = new ReflectionMethod('Varien_Cache_Core', '_isCompressionAllowed');
        $method->setAccessible(true);

        $this->assertEquals($method->invoke($this->_core), $expectedResult);
    }

    public function isCompressionAllowedProvider()
    {
        return array(
            'true bool' => array(true, true),
            'true string' => array('true', true),
            '1 int' => array(1, true),
            '1 string' => array('1', true),
            'false bool' => array(false, false),
            'false string' => array('false', false),
            '0 int' => array(0, false),
            '0 string' => array('0', false),
            'any string' => array('any', false),
        );
    }

    public function testCompressData()
    {
        $method = new ReflectionMethod('Varien_Cache_Core', '_compressData');
        $method->setAccessible(true);

        $this->assertStringStartsWith('CACHE_COMPRESSION', $method->invoke($this->_core, 'any string'));
    }

    public function testDecompressData()
    {
        $methodCompress = new ReflectionMethod('Varien_Cache_Core', '_compressData');
        $methodCompress->setAccessible(true);

        $methodDecompress = new ReflectionMethod('Varien_Cache_Core', '_decompressData');
        $methodDecompress->setAccessible(true);

        $string = 'testString';

        $this->assertEquals(
            $string,
            $methodDecompress->invoke($this->_core, $methodCompress->invoke($this->_core, $string))
        );
    }

    public function testIsCompressionNeeded()
    {
        $string = 'Any string';

        $this->_core->setOption('compression_threshold', strlen($string));

        $method = new ReflectionMethod('Varien_Cache_Core', '_isCompressionNeeded');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($this->_core, $string));
        $this->assertFalse($method->invoke($this->_core, substr($string, 0, -1)));
        $this->assertTrue($method->invoke($this->_core, $string . 's'));
    }

    public function testIsDecompressionNeeded()
    {
        $string = 'Any string';
        $prefix = 'CACHE_COMPRESSION';

        $method = new ReflectionMethod('Varien_Cache_Core', '_isDecompressionNeeded');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($this->_core, $string));
        $this->assertFalse($method->invoke($this->_core, 's' . $prefix . $string));
        $this->assertTrue($method->invoke($this->_core, $prefix . $string));
    }

    /**
     * @dataProvider saveLoadProvider
     */
    public function testSaveLoad(array $argumentsArray, array $optionsArray)
    {
        $cacheId = 'cacheId' . rand(1, 100);

        $this->_prepareNSaveCache($cacheId, $argumentsArray, $optionsArray);

        $doNotUnserialize =
            array_key_exists('doNotUnserialize', $argumentsArray) ? $argumentsArray['doNotUnserialize'] : false;

        $this->assertArrayHasKey($cacheId, self::$_cacheStorage);
        $this->assertInternalType('string', self::$_cacheStorage[$cacheId]);

        $loadedValue = $this->_core->load($cacheId, false, $doNotUnserialize);

        $loadedValue = $doNotUnserialize ? unserialize($loadedValue) : $loadedValue;

        $this->assertEquals($argumentsArray['data'], $loadedValue);
    }

    public function saveLoadProvider()
    {
        return array(
            'no_compress' => array(
                array('data' => str_repeat('s', 10)),
                array('compression' => false)
            ),
            'compress_short_string' => array(
                array('data' => str_repeat('s', 10)),
                array('compression' => true)
            ),
            'compress_long_string' => array(
                array('data' => str_repeat('s', 1000)),
                array('compression' => true)
            ),
            'compress_short_array' => array(
                array('data' => array(str_repeat('s', 10))),
                array('compression' => true, 'automatic_serialization' => true)
            ),
            'compress_long_array' => array(
                array('data' => array(str_repeat('s', 1000))),
                array('compression' => true, 'automatic_serialization' => true)
            ),
            'no_compress_array' => array(
                array('data' => array(str_repeat('s', 10))),
                array('compression' => false, 'automatic_serialization' => true)
            ),
            'compress_short_array_no_serialize' => array(
                array('data' => array(str_repeat('s', 10)), 'doNotUnserialize' => true),
                array('compression' => true, 'automatic_serialization' => true)
            ),
        );
    }

    /**
     * Just not to copy-paste logic in two methods
     *
     * @param $cacheId
     * @param $argumentsArray
     * @param $optionsArray
     */
    protected function _prepareNSaveCache($cacheId, $argumentsArray, $optionsArray)
    {
        $backend = $this->getMock('Zend_Cache_Backend', array('save', 'load'));
        $backend->expects($this->any())
            ->method('save')
            ->will($this->returnCallback(array(__CLASS__, 'mockSave')));

        $backend->expects($this->any())
            ->method('load')
            ->will($this->returnCallback(array(__CLASS__, 'mockLoad')));

        $this->_core->setBackend($backend);
        $this->_core->setOption('write_control', false);
        $this->_core->setOption('automatic_cleaning_factor', 0);

        if (array_key_exists('automatic_serialization', $optionsArray)) {
            $this->_core->setOption('automatic_serialization', $optionsArray['automatic_serialization']);
        }

        $this->_core->setOption('compression', $optionsArray['compression']);

        $this->_core->save($argumentsArray['data'], $cacheId);
    }

    /**
     * @expectedException Zend_Cache_Exception
     */
    public function testSaveLoadError()
    {
        $cacheId = 'cacheId' . rand(1, 100);

        $argumentsArray = array('data' => array(str_repeat('s', 10)));
        $optionsArray = array('compression' => true);

        $this->_prepareNSaveCache($cacheId, $argumentsArray, $optionsArray);
    }

    public static function mockSave($data, $cacheId)
    {
        self::$_cacheStorage[$cacheId] = $data;
        return true;
    }

    public static function mockLoad($cacheId)
    {
        return array_key_exists($cacheId, self::$_cacheStorage) ? self::$_cacheStorage[$cacheId] : null;
    }
}
