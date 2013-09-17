<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Cms_Model_Wysiwyg_Images_StorageTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected static $_baseDir;

    public static function setUpBeforeClass()
    {
        self::$_baseDir = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->get('Magento_Cms_Helper_Wysiwyg_Images')
                ->getCurrentPath() . __CLASS__;
        mkdir(self::$_baseDir, 0777);
        touch(self::$_baseDir . DIRECTORY_SEPARATOR . '1.swf');
    }

    public static function tearDownAfterClass()
    {
        Magento_Io_File::rmdirRecursive(self::$_baseDir);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetFilesCollection()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setDesignTheme('magento_basic', 'adminhtml');
        /** @var $model Magento_Cms_Model_Wysiwyg_Images_Storage */
        $model = Mage::getModel('Magento_Cms_Model_Wysiwyg_Images_Storage');
        $collection = $model->getFilesCollection(self::$_baseDir, 'media');
        $this->assertInstanceOf('Magento_Cms_Model_Wysiwyg_Images_Storage_Collection', $collection);
        foreach ($collection as $item) {
            $this->assertInstanceOf('Magento_Object', $item);
            $this->assertStringEndsWith('/1.swf', $item->getUrl());
            $this->assertStringMatchesFormat(
                'http://%s/static/adminhtml/%s/%s/Magento_Cms/images/placeholder_thumbnail.jpg',
                $item->getThumbUrl()
            );
            return;
        }
    }

    public function testGetThumbsPath()
    {
        $model = Mage::getModel('Magento_Cms_Model_Wysiwyg_Images_Storage');
        $this->assertStringStartsWith(
            realpath(Magento_TestFramework_Helper_Bootstrap::getInstance()->getAppInstallDir()),
            $model->getThumbsPath()
        );
    }
}
