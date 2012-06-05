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

class Mage_Core_Model_Design_Fallback_MapTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested model
     *
     * @var Mage_Core_Model_Design_Fallback_Map
     */
    protected $_model;

    /**
     * @var string
     */
    protected static $_tmpDir;

    public static function setUpBeforeClass()
    {
        self::$_tmpDir = Magento_Test_Environment::getInstance()->getTmpDir() . DIRECTORY_SEPARATOR . 'fallback';
        if (!file_exists(self::$_tmpDir)) {
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

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Design_Fallback_Map(self::$_tmpDir);
    }

    public function tearDown()
    {
        Magento_Test_Environment::getInstance()->cleanDir(self::$_tmpDir);
    }

    public function testGetSetFilePath()
    {
        $file = 'file.xml';
        $area = 'area';
        $package = 'package';
        $theme = 'theme';
        $skin = 'skin';
        $locale = 'locale';
        $module = 'module';

        // Empty at first
        $this->assertNull($this->_model->getFilePath($file, $area, $package, $theme, $skin, $locale, $module));

        // Store something
        $filePath = 'path/to/file.xml';
        $result = $this->_model->setFilePath($file, $area, $package, $theme, $skin, $locale, $module, $filePath);
        $this->assertSame($this->_model, $result);

        // Stored successfully
        $storedFilePath = $this->_model->getFilePath($file, $area, $package, $theme, $skin, $locale, $module);
        $this->assertEquals($filePath, $storedFilePath);

        // Other combination of params still gives nothing
        $this->assertNull($this->_model->getFilePath($file, 'other', $package, $theme, $skin, $locale, $module));
    }

    /**
     * @depends testGetSetFilePath
     */
    public function testGetSetFilePathWithSaving()
    {
        $file = 'file.xml';
        $area = 'area';
        $package = 'package';
        $theme = 'theme';
        $skin = 'skin';
        $locale = 'locale';
        $module = 'module';
        $filePath = 'path/to/file.xml';

        $this->_model->setFilePath($file, $area, $package, $theme, $skin, $locale, $module, $filePath)
            ->save();

        $newModel = new Mage_Core_Model_Design_Fallback_Map(self::$_tmpDir);
        $storedFilePath = $newModel->getFilePath($file, $area, $package, $theme, $skin, $locale, $module);
        $this->assertEquals($filePath, $storedFilePath);
    }

    /**
     * Verifies, that map file segmentation is performed according to area, package, theme, skin and locale.
     *
     * @depends testGetSetFilePathWithSaving
     */
    public function testSaveSegmentation()
    {
        $globPath = self::$_tmpDir . DIRECTORY_SEPARATOR . '*.*';
        $this->assertEmpty(glob($globPath));

        $this->_model
            ->setFilePath('file.xml', 'area', 'package', 'theme', 'skin', 'locale', 'module', 'path/to/file.xml')
            ->save();
        $this->assertCount(1, glob($globPath));

        $this->_model
            ->setFilePath('file.xml', 'other_area', 'package', 'theme', 'skin', 'locale', 'module', 'path/to/file.xml')
            ->save();
        $this->assertCount(2, glob($globPath));

        $this->_model
            ->setFilePath('file.xml', 'area', 'other_package', 'theme', 'skin', 'locale', 'module', 'path/to/file.xml')
            ->save();
        $this->assertCount(3, glob($globPath));

        $this->_model
            ->setFilePath('file.xml', 'area', 'package', 'other_theme', 'skin', 'locale', 'module', 'path/to/file.xml')
            ->save();
        $this->assertCount(4, glob($globPath));

        $this->_model
            ->setFilePath('file.xml', 'area', 'package', 'theme', 'other_skin', 'locale', 'module', 'path/to/file.xml')
            ->save();
        $this->assertCount(5, glob($globPath));

        $this->_model
            ->setFilePath('file.xml', 'area', 'package', 'theme', 'skin', 'other_locale', 'module', 'path/to/file.xml')
            ->save();
        $this->assertCount(6, glob($globPath));

        $this->_model
            ->setFilePath('file.xml', 'area', 'package', 'theme', 'skin', 'locale', 'other_module', 'path/to/file.xml')
            ->save();
        $this->assertCount(6, glob($globPath));
    }
}
