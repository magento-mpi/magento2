<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Page\Asset;

use Magento\Filesystem,
    Magento\Filesystem\DirectoryList,
    Magento\Filesystem\Directory\Write;

class MergeServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Page\Asset\MergeService
     */
    protected $_object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var Write | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_directory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirs;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_state;

    protected function setUp()
    {
        $this->_objectManager = $this->getMockForAbstractClass('Magento\ObjectManager', array('create'));
        $this->_storeConfig = $this->getMock(
            'Magento\Core\Model\Store\Config', array('getConfigFlag'), array(), '', false
        );
        $this->_filesystem = $this->getMock('Magento\Filesystem',
            array('getDirectoryWrite'), array(), '', false);
        $this->_directory = $this->getMock(
            'Magento\Filesystem\Directory\Write',
            array('delete', 'getRelativePath'), array(), '', false
        );
        $this->_dirs = $this->getMock('Magento\App\Dir', array(), array(), '', false);
        $this->_state = $this->getMock('Magento\App\State', array(), array(), '', false);

        $this->_object = new \Magento\Core\Model\Page\Asset\MergeService(
            $this->_objectManager,
            $this->_storeConfig,
            $this->_filesystem,
            $this->_dirs,
            $this->_state
        );
    }

    /**
     * @expectedException \InvalidArgumentException
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
     * @param string $appMode
     * @param string $mergeStrategy
     * @dataProvider getMergedAssetsDataProvider
     */
    public function testGetMergedAssets(array $assets, $contentType, $storeConfigPath, $appMode, $mergeStrategy)
    {
        $mergedAsset = $this->getMock('Magento\Core\Model\Page\Asset\AssetInterface');
        $this->_storeConfig
            ->expects($this->any())
            ->method('getConfigFlag')
            ->will($this->returnValueMap(array(
                array($storeConfigPath, null, true),
            )))
        ;

        $mergeStrategyMock = $this->getMock($mergeStrategy, array(), array(), '', false);

        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with(
                'Magento\Core\Model\Page\Asset\Merged',
                array('assets' => $assets, 'mergeStrategy' => $mergeStrategyMock)
            )
            ->will($this->returnValue($mergedAsset))
        ;

        $this->_objectManager
            ->expects($this->once())
            ->method('get')
            ->with($mergeStrategy)
            ->will($this->returnValue($mergeStrategyMock))
        ;
        $this->_state
            ->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($appMode));
        $this->assertSame($mergedAsset, $this->_object->getMergedAssets($assets, $contentType));
    }

    public static function getMergedAssetsDataProvider()
    {
        $jsAssets = array(
            new \Magento\Core\Model\Page\Asset\Remote('http://127.0.0.1/magento/script_one.js'),
            new \Magento\Core\Model\Page\Asset\Remote('http://127.0.0.1/magento/script_two.js')
        );
        $cssAssets = array(
            new \Magento\Core\Model\Page\Asset\Remote('http://127.0.0.1/magento/style_one.css'),
            new \Magento\Core\Model\Page\Asset\Remote('http://127.0.0.1/magento/style_two.css')
        );
        return array(
            'js production mode' => array(
                $jsAssets,
                \Magento\View\Publisher::CONTENT_TYPE_JS,
                \Magento\Core\Model\Page\Asset\MergeService::XML_PATH_MERGE_JS_FILES,
                \Magento\App\State::MODE_PRODUCTION,
                'Magento\Core\Model\Page\Asset\MergeStrategy\FileExists'
            ),
            'css production mode' => array(
                $cssAssets,
                \Magento\View\Publisher::CONTENT_TYPE_CSS,
                \Magento\Core\Model\Page\Asset\MergeService::XML_PATH_MERGE_CSS_FILES,
                \Magento\App\State::MODE_PRODUCTION,
                'Magento\Core\Model\Page\Asset\MergeStrategy\FileExists'
            ),
            'js default mode' => array(
                $jsAssets,
                \Magento\View\Publisher::CONTENT_TYPE_JS,
                \Magento\Core\Model\Page\Asset\MergeService::XML_PATH_MERGE_JS_FILES,
                \Magento\App\State::MODE_DEFAULT,
                'Magento\Core\Model\Page\Asset\MergeStrategy\Checksum'
            ),
            'css default mode' => array(
                $cssAssets,
                \Magento\View\Publisher::CONTENT_TYPE_CSS,
                \Magento\Core\Model\Page\Asset\MergeService::XML_PATH_MERGE_CSS_FILES,
                \Magento\App\State::MODE_DEFAULT,
                'Magento\Core\Model\Page\Asset\MergeStrategy\Checksum'
            ),
            'js developer mode' => array(
                $jsAssets,
                \Magento\View\Publisher::CONTENT_TYPE_JS,
                \Magento\Core\Model\Page\Asset\MergeService::XML_PATH_MERGE_JS_FILES,
                \Magento\App\State::MODE_DEVELOPER,
                'Magento\Core\Model\Page\Asset\MergeStrategy\Checksum'
            ),
            'css developer mode' => array(
                $cssAssets,
                \Magento\View\Publisher::CONTENT_TYPE_CSS,
                \Magento\Core\Model\Page\Asset\MergeService::XML_PATH_MERGE_CSS_FILES,
                \Magento\App\State::MODE_DEVELOPER,
                'Magento\Core\Model\Page\Asset\MergeStrategy\Checksum'
            ),
        );
    }

    public function testCleanMergedJsCss()
    {
        $this->_filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::PUB_VIEW_CACHE)
            ->will($this->returnValue($this->_directory));

        $mergedDir = '_merged';
        $this->_directory->expects($this->once())
            ->method('getRelativePath')
            ->with(\Magento\Core\Model\Page\Asset\Merged::PUBLIC_MERGE_DIR)
            ->will($this->returnValue($mergedDir));

        $this->_directory->expects($this->once())
            ->method('delete')
            ->with($mergedDir);

        $mediaStub = $this->getMock('Magento\Core\Helper\File\Storage\Database', array(), array(), '', false);
        $mediaStub->expects($this->once())
            ->method('deleteFolder')
            ->with($mergedDir);
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with('Magento\Core\Helper\File\Storage\Database')
            ->will($this->returnValue($mediaStub));

        $this->_object->cleanMergedJsCss();
    }
}
