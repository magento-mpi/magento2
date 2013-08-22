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
     * @var Magento_Core_Controller_Request_Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Theme_Helper_Storage
     */
    protected $_helperStorage;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Theme_Model_Wysiwyg_Storage
     */
    protected $_storageModel;

    public function setUp()
    {
        $this->_objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $this->_filesystem = $this->_objectManager->get('Magento_Filesystem');
        $this->_filesystem->setIsAllowCreateDirectories(true);

        /** @var $theme Magento_Core_Model_Theme */
        $theme = $this->_objectManager->create('Magento_Core_Model_Theme')->getCollection()->getFirstItem();

        /** @var $request Magento_Core_Controller_Request_Http */
        $request = $this->_objectManager->get('Magento_Core_Controller_Request_Http');
        $request->setParam(Magento_Theme_Helper_Storage::PARAM_THEME_ID, $theme->getId());
        $request->setParam(Magento_Theme_Helper_Storage::PARAM_CONTENT_TYPE,
            Magento_Theme_Model_Wysiwyg_Storage::TYPE_IMAGE);

        $this->_helperStorage = $this->_objectManager->get('Magento_Theme_Helper_Storage');

        $this->_storageModel = $this->_objectManager->create('Magento_Theme_Model_Wysiwyg_Storage', array(
            'helper' => $this->_helperStorage
        ));
    }

    public function tearDown()
    {
        $this->_filesystem->delete($this->_helperStorage->getStorageRoot());
    }

    /**
     * @covers Magento_Theme_Model_Wysiwyg_Storage::_createThumbnail
     */
    public function testCreateThumbnail()
    {
        $image = 'some_image.jpg';
        $imagePath = realpath(__DIR__) . "/_files/theme/image/{$image}";
        $tmpImagePath = $this->_copyFileToTmpCustomizationPath($imagePath);

        $method = $this->_getMethod('_createThumbnail');
        $result = $method->invokeArgs($this->_storageModel, array($tmpImagePath));

        $expectedResult = $this->_helperStorage->getThumbnailDirectory($tmpImagePath)
            . Magento_Filesystem::DIRECTORY_SEPARATOR . $image;

        $this->assertEquals($expectedResult, $result);
        $this->assertFileExists($result);
    }

    /**
     * @param string $name
     * @return ReflectionMethod
     */
    protected function _getMethod($name)
    {
        $class = new ReflectionClass('Magento_Theme_Model_Wysiwyg_Storage');
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
            . Magento_Filesystem::DIRECTORY_SEPARATOR
            . basename($sourceFile);

        $this->_filesystem->ensureDirectoryExists(pathinfo($targetFile, PATHINFO_DIRNAME));
        $this->_filesystem->copy($sourceFile, $targetFile);
        return $targetFile;
    }
}
