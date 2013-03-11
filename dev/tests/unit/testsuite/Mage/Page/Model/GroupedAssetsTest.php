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

class Mage_Page_Model_GroupedAssetsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Page_Model_GroupedAssets
     */
    protected $_object;

    /**
     * @var Mage_Core_Model_Asset_AssetInterface
     */
    protected $_asset;

    /**
     * @var Mage_Core_Model_Asset_Collection
     */
    protected $_pageAssets;

    protected function setUp()
    {
        $this->_pageAssets = new Mage_Core_Model_Asset_Collection();
        $page = $this->getMock('Mage_Core_Model_Page', array('getAssets'));
        $page->expects($this->once())->method('getAssets')->will($this->returnValue($this->_pageAssets));

        $this->_object = new Mage_Page_Model_GroupedAssets($page);
        $this->_asset = new Mage_Core_Model_Asset_Remote('http://127.0.0.1/magento/test.css');
        $this->_object->addAsset('asset', $this->_asset);
    }

    /**
     * Assert that actual asset groups equal to expected ones
     *
     * @param array $expectedGroups
     * @param array $actualGroupObjects
     */
    protected function _assertGroups(array $expectedGroups, array $actualGroupObjects)
    {
        $this->assertInternalType('array', $actualGroupObjects);
        $actualGroups = array();
        /** @var $actualGroup Mage_Page_Model_Asset_Group */
        foreach ($actualGroupObjects as $actualGroup) {
            $this->assertInstanceOf('Mage_Page_Model_Asset_Group', $actualGroup);
            $actualGroups[] = array(
                'properties' => $actualGroup->getProperties(),
                'assets' => $actualGroup->getAll(),
            );
        }
        $this->assertEquals($expectedGroups, $actualGroups);
    }

    public function testAddAsset()
    {
        $assetNew = new Mage_Core_Model_Asset_Remote('http://127.0.0.1/magento/test_new.css');
        $this->_object->addAsset('asset_new', $assetNew, array('test_property' => 'test_value'));
        $this->assertEquals(array('asset' => $this->_asset, 'asset_new' => $assetNew), $this->_pageAssets->getAll());
    }

    public function testRemoveAsset()
    {
        $this->_object->removeAsset('asset');
        $this->assertEquals(array(), $this->_pageAssets->getAll());
    }

    public function testGroupByProperties()
    {
        $cssAsset = new Mage_Core_Model_Asset_Remote('http://127.0.0.1/style.css', 'css');
        $jsAsset = new Mage_Core_Model_Asset_Remote('http://127.0.0.1/script.js', 'js');
        $jsAssetAllowingMerge = $this->getMockForAbstractClass('Mage_Core_Model_Asset_MergeInterface');
        $jsAssetAllowingMerge->expects($this->any())->method('getContentType')->will($this->returnValue('js'));

        // assets with identical properties should be grouped together
        $this->_object->addAsset('css_asset_one', $cssAsset, array('property' => 'test_value'));
        $this->_object->addAsset('css_asset_two', $cssAsset, array('property' => 'test_value'));

        // assets with different properties should go to different groups
        $this->_object->addAsset('css_asset_three', $cssAsset, array('property' => 'different_value'));
        $this->_object->addAsset('js_asset_one', $jsAsset, array('property' => 'test_value'));
        $this->_object->addAsset(
            'js_asset_two',
            $jsAsset,
            array('property' => 'test_value', 'unique_property' => 'unique_value')
        );

        // assets allowing merge should go to separate group regardless of having identical properties
        $this->_object->addAsset('asset_allowing_merge', $jsAssetAllowingMerge, array('property' => 'test_value'));

        $expectedGroups = array(
            array(
                'properties' => array('content_type' => 'unknown', 'can_merge' => 0),
                'assets' => array('asset' => $this->_asset),
            ),
            array(
                'properties' => array('property' => 'test_value', 'content_type' => 'css', 'can_merge' => 0),
                'assets' => array('css_asset_one' => $cssAsset, 'css_asset_two' => $cssAsset),
            ),
            array(
                'properties' => array('property' => 'different_value', 'content_type' => 'css', 'can_merge' => 0),
                'assets' => array('css_asset_three' => $cssAsset),
            ),
            array(
                'properties' => array('property' => 'test_value', 'content_type' => 'js', 'can_merge' => 0),
                'assets' => array('js_asset_one' => $jsAsset),
            ),
            array(
                'properties' => array(
                    'property' => 'test_value',
                    'unique_property' => 'unique_value',
                    'content_type' => 'js',
                    'can_merge' => 0,
                ),
                'assets' => array('js_asset_two' => $jsAsset),
            ),
            array(
                'properties' => array('property' => 'test_value', 'content_type' => 'js', 'can_merge' => 1),
                'assets' => array('asset_allowing_merge' => $jsAssetAllowingMerge),
            ),
        );

        $this->_assertGroups($expectedGroups, $this->_object->groupByProperties());
    }
}
