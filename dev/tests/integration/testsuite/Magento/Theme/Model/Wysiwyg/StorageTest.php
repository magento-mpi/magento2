<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Storage model test
 */
namespace Magento\Theme\Model\Wysiwyg;

use Magento\Framework\App\Filesystem\DirectoryList;

class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\RequestInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Theme\Helper\Storage
     */
    protected $_helperStorage;

    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Theme\Model\Wysiwyg\Storage
     */
    protected $_storageModel;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $directoryTmp;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $directoryVar;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $directoryList = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\Filesystem\DirectoryList'
        );

        $dirPath = ltrim(str_replace($directoryList->getRoot(), '', str_replace('\\', '/', __DIR__)) . '/_files', '/');

        $tmpDirPath = ltrim(
            str_replace(
                $directoryList->getRoot(),
                '',
                str_replace('\\', '/', realpath(__DIR__ . '/../../../../../tmp'))
            ),
            '/'
        );

        $directoryList->addDirectory(DirectoryList::VAR_DIR, array('path' => $dirPath));
        $directoryList->addDirectory(DirectoryList::TMP, array('path' => $tmpDirPath));
        $directoryList->addDirectory(DirectoryList::MEDIA, array('path' => $tmpDirPath));

        $this->_filesystem = $this->_objectManager->create(
            'Magento\Framework\App\Filesystem',
            array('directoryList' => $directoryList)
        );
        $this->directoryVar = $this->_filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->directoryTmp = $this->_filesystem->getDirectoryWrite(DirectoryList::TMP);

        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $this->_objectManager->create('Magento\Framework\View\Design\ThemeInterface')
            ->getCollection()
            ->getFirstItem();

        /** @var $request \Magento\Framework\App\Request\Http */
        $request = $this->_objectManager->get('Magento\Framework\App\Request\Http');
        $request->setParam(\Magento\Theme\Helper\Storage::PARAM_THEME_ID, $theme->getId());
        $request->setParam(
            \Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE,
            \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE
        );

        $this->_helperStorage = $this->_objectManager->get('Magento\Theme\Helper\Storage');

        $this->_storageModel = $this->_objectManager->create(
            'Magento\Theme\Model\Wysiwyg\Storage',
            array('helper' => $this->_helperStorage, 'filesystem' => $this->_filesystem)
        );
    }

    protected function tearDown()
    {
        $this->directoryTmp->delete($this->directoryTmp->getRelativePath($this->_helperStorage->getStorageRoot()));
    }

    /**
     * @covers \Magento\Theme\Model\Wysiwyg\Storage::_createThumbnail
     */
    public function testCreateThumbnail()
    {
        $image = 'some_image.jpg';
        $imagePath = realpath(__DIR__) . "/_files/theme/image/{$image}";
        $tmpImagePath = $this->_copyFileToTmpCustomizationPath($imagePath);

        $relativePath = $this->directoryTmp->getRelativePath($tmpImagePath);
        $method = $this->_getMethod('_createThumbnail');
        $result = $method->invokeArgs($this->_storageModel, array($relativePath));

        $expectedResult = $this->directoryTmp->getRelativePath(
            $this->_helperStorage->getThumbnailDirectory($tmpImagePath) . '/' . $image
        );

        $this->assertEquals($expectedResult, $result);
        $this->assertFileExists($this->directoryTmp->getAbsolutePath($result));
    }

    /**
     * @param string $name
     * @return \ReflectionMethod
     */
    protected function _getMethod($name)
    {
        $class = new \ReflectionClass('Magento\Theme\Model\Wysiwyg\Storage');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * Copy file to tmp theme customization path
     *
     * @param string $sourceFile
     * @return string
     */
    protected function _copyFileToTmpCustomizationPath($sourceFile)
    {
        $targetFile = $this->_helperStorage->getStorageRoot() . '/' . basename($sourceFile);
        $this->directoryTmp->create(pathinfo($targetFile, PATHINFO_DIRNAME));
        $this->directoryVar->copyFile(
            $this->directoryVar->getRelativePath($sourceFile),
            $this->directoryTmp->getRelativePath($targetFile),
            $this->directoryTmp
        );
        return $targetFile;
    }
}
