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
class Magento_Theme_Model_Wysiwyg_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Controller\Request\Http|PHPUnit_Framework_MockObject_MockObject
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

    public function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_filesystem = $this->_objectManager->get('Magento\Filesystem');
        $this->_filesystem->setIsAllowCreateDirectories(true);

        /** @var $theme \Magento\Core\Model\Theme */
        $theme = $this->_objectManager->create('Magento\Core\Model\Theme')->getCollection()->getFirstItem();

        /** @var $request \Magento\Core\Controller\Request\Http */
        $request = $this->_objectManager->get('Magento\Core\Controller\Request\Http');
        $request->setParam(\Magento\Theme\Helper\Storage::PARAM_THEME_ID, $theme->getId());
        $request->setParam(\Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE,
            \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE);

        $this->_helperStorage = $this->_objectManager->get('Magento\Theme\Helper\Storage');

        $this->_storageModel = $this->_objectManager->create('Magento\Theme\Model\Wysiwyg\Storage', array(
            'helper' => $this->_helperStorage
        ));
    }

    public function tearDown()
    {
        $this->_filesystem->delete($this->_helperStorage->getStorageRoot());
    }

    /**
     * @covers \Magento\Theme\Model\Wysiwyg\Storage::_createThumbnail
     */
    public function testCreateThumbnail()
    {
        $image = 'some_image.jpg';
        $imagePath = realpath(__DIR__) . "/_files/theme/image/{$image}";
        $tmpImagePath = $this->_copyFileToTmpCustomizationPath($imagePath);

        $method = $this->_getMethod('_createThumbnail');
        $result = $method->invokeArgs($this->_storageModel, array($tmpImagePath));

        $expectedResult = $this->_helperStorage->getThumbnailDirectory($tmpImagePath)
            . \Magento\Filesystem::DIRECTORY_SEPARATOR . $image;

        $this->assertEquals($expectedResult, $result);
        $this->assertFileExists($result);
    }

    /**
     * @param string $name
     * @return ReflectionMethod
     */
    protected function _getMethod($name)
    {
        $class = new ReflectionClass('Magento\Theme\Model\Wysiwyg\Storage');
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
            . \Magento\Filesystem::DIRECTORY_SEPARATOR
            . basename($sourceFile);

        $this->_filesystem->ensureDirectoryExists(pathinfo($targetFile, PATHINFO_DIRNAME));
        $this->_filesystem->copy($sourceFile, $targetFile);
        return $targetFile;
    }
}
