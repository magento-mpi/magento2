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

class Mage_Core_Model_Page_Asset_MergeServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Page_Asset_MergeService
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfig;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirs;

    public function setUp()
    {
        $this->_objectManager = $this->getMockForAbstractClass('Magento_ObjectManager', array('create'));
        $this->_storeConfig = $this->getMock('Mage_Core_Model_Store_Config', array('getConfigFlag'));
        $this->_filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);

        $this->_object = new Mage_Core_Model_Page_Asset_MergeService(
            $this->_objectManager,
            $this->_storeConfig,
            $this->_filesystem,
            $this->_dirs
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Merge for content type 'unknown' is not supported.
     */
    public function testGetMergedAssetsWrongContentType()
    {
        $this->_object->getMergedAssets(array(), 'unknown');
    }

    /**
     * @param array $assets
     * @param string $contentType
     * @param string $storeConfigPath
     * @dataProvider getMergedAssetsDataProvider
     */
    public function testGetMergedAssets(array $assets, $contentType, $storeConfigPath)
    {
        $mergedAsset = $this->getMock('Mage_Core_Model_Page_Asset_AssetInterface');
        $this->_storeConfig
            ->expects($this->any())
            ->method('getConfigFlag')
            ->will($this->returnValueMap(array(
                array($storeConfigPath, null, true),
            )))
        ;
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_Merged', array('assets' => $assets))
            ->will($this->returnValue($mergedAsset))
        ;
        $this->assertSame($mergedAsset, $this->_object->getMergedAssets($assets, $contentType));
    }

    public static function getMergedAssetsDataProvider()
    {
        $jsAssets = array(
            new Mage_Core_Model_Page_Asset_Remote('http://127.0.0.1/magento/script_one.js'),
            new Mage_Core_Model_Page_Asset_Remote('http://127.0.0.1/magento/script_two.js')
        );
        $cssAssets = array(
            new Mage_Core_Model_Page_Asset_Remote('http://127.0.0.1/magento/style_one.css'),
            new Mage_Core_Model_Page_Asset_Remote('http://127.0.0.1/magento/style_two.css')
        );
        return array(
            'js' => array(
                $jsAssets,
                Mage_Core_Model_Design_PackageInterface::CONTENT_TYPE_JS,
                Mage_Core_Model_Page_Asset_MergeService::XML_PATH_MERGE_JS_FILES,
            ),
            'css' => array(
                $cssAssets,
                Mage_Core_Model_Design_PackageInterface::CONTENT_TYPE_CSS,
                Mage_Core_Model_Page_Asset_MergeService::XML_PATH_MERGE_CSS_FILES,
            ),
        );
    }

    public function testCleanMergedJsCss()
    {
        $this->_dirs->expects($this->once())
            ->method('getDir')
            ->with(Mage_Core_Model_Dir::PUB_VIEW_CACHE)
            ->will($this->returnValue('/pub/cache'));

        $mergedDir = '/pub/cache/' . Mage_Core_Model_Page_Asset_Merged::PUBLIC_MERGE_DIR;
        $this->_filesystem->expects($this->once())
            ->method('delete')
            ->with($mergedDir, null);

        $mediaStub = $this->getMock('Mage_Core_Helper_File_Storage_Database', array(), array(), '', false);
        $mediaStub->expects($this->once())
            ->method('deleteFolder')
            ->with($mergedDir);
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with('Mage_Core_Helper_File_Storage_Database')
            ->will($this->returnValue($mediaStub));

        $this->_object->cleanMergedJsCss();
    }
}
