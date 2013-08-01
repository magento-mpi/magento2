<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Cms_Model_Wysiwyg_Images_StorageTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected static $_baseDir;

    public static function setUpBeforeClass()
    {
        self::$_baseDir = Mage::helper('Mage_Cms_Helper_Wysiwyg_Images')->getCurrentPath() . __CLASS__;
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
        Mage::getDesign()->setDesignTheme('default/basic', 'adminhtml');
        /** @var $model Mage_Cms_Model_Wysiwyg_Images_Storage */
        $model = Mage::getModel('Mage_Cms_Model_Wysiwyg_Images_Storage');
        $collection = $model->getFilesCollection(self::$_baseDir, 'media');
        $this->assertInstanceOf('Mage_Cms_Model_Wysiwyg_Images_Storage_Collection', $collection);
        foreach ($collection as $item) {
            $this->assertInstanceOf('Magento_Object', $item);
            $this->assertStringEndsWith('/1.swf', $item->getUrl());
            $this->assertStringMatchesFormat(
                'http://%s/static/adminhtml/%s/%s/%s/Mage_Cms/images/placeholder_thumbnail.jpg',
                $item->getThumbUrl()
            );
            return;
        }
    }

    public function testGetThumbsPath()
    {
        $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local);
        $objectManager = Mage::getObjectManager();
        $imageFactory = $objectManager->get('Mage_Core_Model_Image_AdapterFactory');
        $viewUrl = $objectManager->get('Mage_Core_Model_View_Url');
        $model = new Mage_Cms_Model_Wysiwyg_Images_Storage($filesystem, $imageFactory, $viewUrl);
        $this->assertStringStartsWith(
            realpath(Magento_Test_Helper_Bootstrap::getInstance()->getAppInstallDir()),
            $model->getThumbsPath()
        );
    }
}
