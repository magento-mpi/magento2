<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Storage model test
 */
namespace Magento\Theme\Model\Wysiwyg;

class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\RequestInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Theme\Helper\Storage
     */
    protected $_helperStorage;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Theme\Model\Wysiwyg\Storage
     */
    protected $_storageModel;

    /**
     * @var \Magento\Filesystem\Directory\Write
     */
    protected $directoryTmp;

    /**
     * @var \Magento\Filesystem\Directory\Write
     */
    protected $directoryVar;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $directoryList = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Filesystem\DirectoryList');

        $dirPath = ltrim(str_replace($directoryList->getRoot(), '', str_replace('\\', '/', __DIR__)) . '/_files', '/');

        $tmpDirPath = ltrim(str_replace($directoryList->getRoot(), '',
            str_replace('\\', '/', realpath(__DIR__ . '/../../../../../tmp'))), '/');

        $directoryList->addDirectory(\Magento\Filesystem::VAR_DIR, array('path' => $dirPath));
        $directoryList->addDirectory(\Magento\Filesystem::TMP, array('path' => $tmpDirPath));
        $directoryList->addDirectory(\Magento\Filesystem::MEDIA, array('path' => $tmpDirPath));

        $this->_filesystem = $this->_objectManager->get('Magento\Filesystem');
        $this->directoryVar = $this->_filesystem->getDirectoryWrite(\Magento\Filesystem::VAR_DIR);
        $this->directoryTmp = $this->_filesystem->getDirectoryWrite(\Magento\Filesystem::TMP);

        /** @var $theme \Magento\View\Design\ThemeInterface */
        $theme = $this->_objectManager->create('Magento\View\Design\ThemeInterface')->getCollection()->getFirstItem();

        /** @var $request \Magento\App\Request\Http */
        $request = $this->_objectManager->get('Magento\App\Request\Http');
        $request->setParam(\Magento\Theme\Helper\Storage::PARAM_THEME_ID, $theme->getId());
        $request->setParam(\Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE,
            \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE);

        $this->_helperStorage = $this->_objectManager->get('Magento\Theme\Helper\Storage');

        $this->_storageModel = $this->_objectManager->create('Magento\Theme\Model\Wysiwyg\Storage', array(
            'helper' => $this->_helperStorage
        ));
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
            $this->_helperStorage->getThumbnailDirectory($tmpImagePath)
            . '/' . $image);

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
        $targetFile = $this->_helperStorage->getStorageRoot()
            . '/' . basename($sourceFile);
        $this->directoryTmp->create(pathinfo($targetFile, PATHINFO_DIRNAME));
        $this->directoryVar->copyFile(
            $this->directoryVar->getRelativePath($sourceFile),
            $this->directoryTmp->getRelativePath($targetFile),
            $this->directoryTmp
        );
        return $targetFile;
    }
}
