<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Page_Asset_MinifyServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Store_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfig;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Dir|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirs;

    /**
     * @var Mage_Core_Model_Page_Asset_MinifyService
     */
    protected $_model;

    /**
     * @var Mage_Core_Model_App_State|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appState;

    protected function setUp()
    {
        $configMap = array(
            array('dev/js/minify', null, true),
            array('dev/css/minify', null, false),
        );
        $this->_storeConfig = $this->getMock('Mage_Core_Model_Store_Config');
        $this->_storeConfig->expects($this->any())
            ->method('getConfigFlag')
            ->will($this->returnValueMap($configMap));

        $this->_dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_dirs->expects($this->any())
            ->method('getDir')
            ->with(Mage_Core_Model_Dir::PUB_VIEW_CACHE)
            ->will($this->returnValue(__DIR__ . '/cache'));

        $this->_objectManager = $this->getMock('Magento_ObjectManager');

        $this->_appState = $this->getMock('Mage_Core_Model_App_State');

        $this->_model = new Mage_Core_Model_Page_Asset_MinifyService($this->_storeConfig, $this->_objectManager,
            $this->_dirs, $this->_appState);
    }

    /**
     * @param string $mode
     * @param string $expectedStrategy
     * @dataProvider getAssetsDataProvider
     */
    public function testGetAssets($mode, $expectedStrategy)
    {
        $minifiedAssetOne = $this->getMock('Mage_Core_Model_Page_Asset_Minified', array(), array(), '', false);
        $minifiedAssetTwo = $this->getMock('Mage_Core_Model_Page_Asset_Minified', array(), array(), '', false);
        $minifier = $this->getMock('Magento_Code_Minifier', array(), array(), '', false, false);
        $adapter = $this->getMock('Minify_Adapter', array(), array(), '', false, false);

        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with('Minify_Adapter')
            ->will($this->returnValue($adapter));

        $this->_appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));

        $strategy = $this->getMock($expectedStrategy, array(), array(), '', false);
        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with($expectedStrategy)
            ->will($this->returnValue($strategy));

        $this->_objectManager->expects($this->at(2))
            ->method('create')
            ->with('Magento_Code_Minifier')
            ->will($this->returnValue($minifier));

        $this->_objectManager->expects($this->at(3))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_Minified')
            ->will($this->returnValue($minifiedAssetOne));

        $this->_objectManager->expects($this->at(4))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_Minified')
            ->will($this->returnValue($minifiedAssetTwo));

        $this->_storeConfig->expects($this->once())
            ->method('getConfig')
            ->with('dev/js/minify_adapter')
            ->will($this->returnValue('Minify_Adapter'));

        $assetEnabledType = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $assetEnabledType->expects($this->any())
            ->method('getContentType')
            ->will($this->returnValue('js'));
        $assetEnabledTypeTwo = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $assetEnabledTypeTwo->expects($this->any())
            ->method('getContentType')
            ->will($this->returnValue('js'));
        $assetDisabledType = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $assetDisabledType->expects($this->any())
            ->method('getContentType')
            ->will($this->returnValue('css'));
        $assets = array($assetEnabledType, $assetEnabledTypeTwo, $assetDisabledType);
        $actualAssets = $this->_model->getAssets($assets);

        $this->assertCount(count($assets), $actualAssets);
        $this->assertSame($minifiedAssetOne, $actualAssets[0]);
        $this->assertSame($minifiedAssetTwo, $actualAssets[1]);
        $this->assertSame($assetDisabledType, $actualAssets[2]);
    }

    public function getAssetsDataProvider()
    {
        return array(
            array(Mage_Core_Model_App_State::MODE_PRODUCTION, 'Magento_Code_Minify_Strategy_Lite'),
            array(Mage_Core_Model_App_State::MODE_DEFAULT, 'Magento_Code_Minify_Strategy_Generate'),
            array(Mage_Core_Model_App_State::MODE_DEVELOPER, 'Magento_Code_Minify_Strategy_Generate'),
        );
    }
}
