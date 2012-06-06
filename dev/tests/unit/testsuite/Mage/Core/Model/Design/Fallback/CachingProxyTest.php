<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_Fallback_CachingProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_tmpDir;

    public static function setUpBeforeClass()
    {
        self::$_tmpDir = Magento_Test_Environment::getInstance()->getTmpDir() . DIRECTORY_SEPARATOR . 'fallback';
        if (!is_dir(self::$_tmpDir)) {
            mkdir(self::$_tmpDir);
        } else {
            Magento_Test_Environment::getInstance()->cleanDir(self::$_tmpDir);
        }
    }

    public static function tearDownAfterClass()
    {
        Magento_Test_Environment::getInstance()->cleanDir(self::$_tmpDir);
        rmdir(self::$_tmpDir);
    }

    public function tearDown()
    {
        Magento_Test_Environment::getInstance()->cleanDir(self::$_tmpDir);
    }

    /**
     * Test that proxy, if entry is not found in a map,
     * a) successfully delegates its resolution to a Fallback model
     * b) puts into a cached map, and subsequent calls do not use Fallback model
     *
     * Every call is repeated twice to verify, that fallback is used only once and next time a proper value is returned
     * by cached map.
     */
    public function testOperations()
    {
        $fallback = $this->getMock('Mage_Core_Model_Design_Fallback', array('getFile', 'getLocaleFile', 'getSkinFile'),
            array(), '', false);

        /** @var $model Mage_Core_Model_Design_Fallback_CachingProxy */
        $params = array(
            'area' => 'frontend',
            'package' => 'package',
            'theme' => 'theme',
            'skin' => 'skin',
            'locale' => 'en_US',
            'canSaveMap' => false,
            'mapDir' => self::$_tmpDir,
            'baseDir' => DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . 'dir'
        );
        $model = $this->getMock(
            'Mage_Core_Model_Design_Fallback_CachingProxy',
            array('_getFallback'),
            array($params)
        );
        $model->expects($this->any())
            ->method('_getFallback')
            ->will($this->returnValue($fallback));

        $module = 'Some_Module';

        // getFile()
        $expected = '/base/dir/path/to/theme_file.ext';
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);
        $fallback->expects($this->once())
            ->method('getFile')
            ->with('file.ext', $module)
            ->will($this->returnValue($expected));

        $actual = $model->getFile('file.ext', $module);
        $this->assertEquals($expected, $actual);
        $actual = $model->getFile('file.ext', $module);
        $this->assertEquals($expected, $actual);

        // getLocaleFile()
        $expected = '/base/dir/path/to/locale_file.ext';
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);
        $fallback->expects($this->once())
            ->method('getLocaleFile')
            ->with('file.ext')
            ->will($this->returnValue($expected));

        $actual = $model->getLocaleFile('file.ext');
        $this->assertEquals($expected, $actual);
        $actual = $model->getLocaleFile('file.ext');
        $this->assertEquals($expected, $actual);

        // getSkinFile()
        $expected = '/base/dir/path/to/skin_file.ext';
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);
        $fallback->expects($this->once())
            ->method('getSkinFile')
            ->with('file.ext', $module)
            ->will($this->returnValue($expected));

        $actual = $model->getSkinFile('file.ext', $module);
        $this->assertEquals($expected, $actual);
        $actual = $model->getSkinFile('file.ext', $module);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that proxy caches published skin path, and further calls do not use fallback model
     */
    public function testNotifySkinFilePublished()
    {
        $module = 'Some_Module';
        $file = 'path/to/file.xml';

        $fallback = $this->getMock('Mage_Core_Model_Design_Fallback', array('getSkinFile'), array(), '', false);
        $fallback->expects($this->once())
            ->method('getSkinFile')
            ->with($file, $module)
            ->will($this->returnValue(null));

        /** @var $model Mage_Core_Model_Design_Fallback_CachingProxy */
        $params = array(
            'area' => 'frontend',
            'package' => 'package',
            'theme' => 'theme',
            'skin' => 'skin',
            'locale' => 'en_US',
            'canSaveMap' => false,
            'mapDir' => self::$_tmpDir,
            'baseDir' => ''
        );
        $model = $this->getMock(
            'Mage_Core_Model_Design_Fallback_CachingProxy',
            array('_getFallback'),
            array($params)
        );
        $model->expects($this->any())
            ->method('_getFallback')
            ->will($this->returnValue($fallback));

        // Empty at first
        $this->assertNull($model->getSkinFile($file, $module));

        // Store something
        $publicFilePath = 'public/path/to/file.xml';
        $result = $model->notifySkinFilePublished($publicFilePath, $file, $module);
        $this->assertSame($model, $result);

        // Stored successfully
        $storedFilePath = $model->getSkinFile($file, $module);
        $this->assertEquals($publicFilePath, $storedFilePath);
    }

    /**
     * Tests that proxy saves data between instantiations
     *
     * @depends testOperations
     * @depends testNotifySkinFilePublished
     */
    public function testSaving()
    {
        $module = 'Some_Module';
        $file = 'a/skin_file.ext';
        $expectedPublicFile = '/path/to/skin_file.ext';

        $params = array(
            'area' => 'frontend',
            'package' => 'package',
            'theme' => 'theme',
            'skin' => 'skin',
            'locale' => 'en_US',
            'canSaveMap' => true,
            'mapDir' => self::$_tmpDir,
            'baseDir' => ''
        );

        $model = new Mage_Core_Model_Design_Fallback_CachingProxy($params);
        $model->notifySkinFilePublished($expectedPublicFile, $file, $module);

        $globPath = self::$_tmpDir . DIRECTORY_SEPARATOR . '*.*';
        $this->assertEmpty(glob($globPath));
        unset($model);
        $this->assertNotEmpty(glob($globPath));

        $fallback = $this->getMock('Mage_Core_Model_Design_Fallback', array('getSkinFile'),
            array(), '', false);
        $fallback->expects($this->never())
            ->method('getSkinFile');

        /** @var $model Mage_Core_Model_Design_Fallback_CachingProxy */
        $model = $this->getMock(
            'Mage_Core_Model_Design_Fallback_CachingProxy',
            array('_getFallback'),
            array($params)
        );
        $model->expects($this->any())
            ->method('_getFallback')
            ->will($this->returnValue($fallback));

        $actualPublicFile = $model->getSkinFile($file, $module);
        $this->assertEquals($expectedPublicFile, $actualPublicFile);
    }
}
