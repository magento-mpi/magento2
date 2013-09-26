<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.LongVariable)
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
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Cms_Model_Wysiwyg_Images_Storage');
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
        $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local);
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $session = $objectManager->get('Magento_Backend_Model_Session');
        $backendUrl = $objectManager->get('Magento_Backend_Model_Url');
        $imageFactory = $objectManager->get('Magento_Core_Model_Image_AdapterFactory');
        $viewUrl = $objectManager->get('Magento_Core_Model_View_Url');
        $imageHelper = $objectManager->get('Magento_Cms_Helper_Wysiwyg_Images');
        $coreFileStorageDb = $objectManager->get('Magento_Core_Helper_File_Storage_Database');
        $dir = $objectManager->get('Magento_Core_Model_Dir');
        $storageCollectionFactory = $objectManager->get('Magento_Cms_Model_Wysiwyg_Images_Storage_CollectionFactory');
        $storageFileFactory = $objectManager->get('Magento_Core_Model_File_Storage_FileFactory');
        $storageDatabaseFactory = $objectManager->get('Magento_Core_Model_File_Storage_DatabaseFactory');
        $directoryDatabaseFactory = $objectManager->get('Magento_Core_Model_File_Storage_Directory_DatabaseFactory');
        $uploaderFactory = $objectManager->get('Magento_Core_Model_File_UploaderFactory');

        $model = new Magento_Cms_Model_Wysiwyg_Images_Storage(
            $session,
            $backendUrl,
            $imageHelper,
            $coreFileStorageDb,
            $filesystem,
            $imageFactory,
            $viewUrl,
            $dir,
            $storageCollectionFactory,
            $storageFileFactory,
            $storageDatabaseFactory,
            $directoryDatabaseFactory,
            $uploaderFactory
        );
        $this->assertStringStartsWith(
            realpath(Magento_TestFramework_Helper_Bootstrap::getInstance()->getAppInstallDir()),
            $model->getThumbsPath()
        );
    }
}
