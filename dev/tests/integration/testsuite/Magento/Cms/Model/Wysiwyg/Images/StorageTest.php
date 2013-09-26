<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
namespace Magento\Cms\Model\Wysiwyg\Images;

class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_baseDir;

    public static function setUpBeforeClass()
    {
        self::$_baseDir = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\Cms\Helper\Wysiwyg\Images')
                ->getCurrentPath() . __CLASS__;
        mkdir(self::$_baseDir, 0777);
        touch(self::$_baseDir . DIRECTORY_SEPARATOR . '1.swf');
    }

    public static function tearDownAfterClass()
    {
        \Magento\Io\File::rmdirRecursive(self::$_baseDir);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetFilesCollection()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setDesignTheme('magento_basic', 'adminhtml');
        /** @var $model \Magento\Cms\Model\Wysiwyg\Images\Storage */
        $model = \Mage::getModel('Magento\Cms\Model\Wysiwyg\Images\Storage');
        $collection = $model->getFilesCollection(self::$_baseDir, 'media');
        $this->assertInstanceOf('Magento\Cms\Model\Wysiwyg\Images\Storage\Collection', $collection);
        foreach ($collection as $item) {
            $this->assertInstanceOf('Magento\Object', $item);
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
        $filesystem = new \Magento\Filesystem(new \Magento\Filesystem\Adapter\Local);
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $session = $objectManager->get('Magento\Backend\Model\Session');
        $backendUrl = $objectManager->get('Magento\Backend\Model\Url');
        $imageFactory = $objectManager->get('Magento\Core\Model\Image\AdapterFactory');
        $viewUrl = $objectManager->get('Magento\Core\Model\View\Url');
        $imageHelper = $objectManager->get('Magento\Cms\Helper\Wysiwyg\Images');
        $coreFileStorageDb = $objectManager->get('Magento\Core\Helper\File\Storage\Database');
        $dir = $objectManager->get('Magento\Core\Model\Dir');
        $storageCollectionFactory = $objectManager->get('Magento\Cms\Model\Wysiwyg\Images\Storage\CollectionFactory');
        $storageFileFactory = $objectManager->get('Magento\Core\Model\File\Storage\FileFactory');
        $storageDatabaseFactory = $objectManager->get('Magento\Core\Model\File\Storage\DatabaseFactory');
        $directoryDatabaseFactory = $objectManager->get('Magento\Core\Model\File\Storage\Directory\DatabaseFactory');
        $uploaderFactory = $objectManager->get('Magento\Core\Model\File\UploaderFactory');

        $model = new \Magento\Cms\Model\Wysiwyg\Images\Storage(
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
            realpath(\Magento\TestFramework\Helper\Bootstrap::getInstance()->getAppInstallDir()),
            $model->getThumbsPath()
        );
    }
}
