<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
namespace Magento\Cms\Model\Wysiwyg\Images;

/**
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
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
                ->getCurrentPath() . 'MagentoCmsModelWysiwygImagesStorageTest';
        if (!file_exists(self::$_baseDir)) {
            mkdir(self::$_baseDir, 0777);
        }
        touch(self::$_baseDir . '/1.swf');
    }

    public static function tearDownAfterClass()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Filesystem\Driver\File')->deleteDirectory(self::$_baseDir);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetFilesCollection()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\App')
            ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $objectManager->get('Magento\View\DesignInterface')
            ->setDesignTheme('magento_backend');
        /** @var $model \Magento\Cms\Model\Wysiwyg\Images\Storage */
        $model = $objectManager->create('Magento\Cms\Model\Wysiwyg\Images\Storage');
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

    /**
     * @magentoAppArea adminhtml
     */
    public function testGetThumbsPath()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $filesystem = $objectManager->get('Magento\App\Filesystem');
        $session = $objectManager->get('Magento\Backend\Model\Session');
        $backendUrl = $objectManager->get('Magento\Backend\Model\Url');
        $imageFactory = $objectManager->get('Magento\Image\AdapterFactory');
        $viewUrl = $objectManager->get('Magento\View\Url');
        $imageHelper = $objectManager->get('Magento\Cms\Helper\Wysiwyg\Images');
        $coreFileStorageDb = $objectManager->get('Magento\Core\Helper\File\Storage\Database');
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
            $storageCollectionFactory,
            $storageFileFactory,
            $storageDatabaseFactory,
            $directoryDatabaseFactory,
            $uploaderFactory
        );
        $this->assertStringStartsWith(
            str_replace('\\', '/', $filesystem->getPath(\Magento\App\Filesystem::MEDIA_DIR)),
            $model->getThumbsPath()
        );
    }
}
